<?php

namespace DomainLayer\BulkUploadRequestService;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use DomainLayer\BulkUploadRequestService\Requests\IBulkUploadRequest;
use DomainLayer\BulkUploadRequestService\Requests\INewBulkUploadRequestRequest;
use Carbon\Carbon;
use ConfigurationLayer\ApplicationConfig\ApplicationConfig;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use DomainLayer\BulkUploadRequestService\Requests\INotARobotRequest;
use DomainLayer\BulkUploadRequestService\Requests\ISubmitBulkUploadRequest;
use DomainLayer\ORM\BulkUploadRequest\BulkUploadRequest;
use DomainLayer\ORM\Contributor\Contributor;
use DomainLayer\ORM\SamplingInformation\SamplingInformation;
use DomainLayer\ORM\TimeSeries\BulkUploadedTimeSeries\BulkUploadedTimeSeries;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonService\Exceptions\EEmptyTimeSeries;
use DomainLayer\TimeSeriesManagement\Ingestion\Converters\SoxConverter\Exceptions\ESoxError;
use DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester\Exceptions\EUnsupportedFileExtension;
use DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester\TimeSeriesIngester;
use InfrastructureLayer\Crypto\TokenGenerator\CryptoTokenGenerator\CryptoTokenGenerator;
use InfrastructureLayer\EmailGateway\IEmailGateway;
use InfrastructureLayer\EmailTemplate\EmailTemplate;
use PresentationLayer\Routes\EInvalidInputs;
use ReCaptcha\ReCaptcha;

/**
 * Class BulkUploadRequestService
 * @package DomainLayer
 */
class BulkUploadRequestService
{
    /** entityManager
     *
     *
     *
     * @var EntityManager
     */
    private $entityManager;

    /** tokenGenerator
     *
     *
     *
     * @var CryptoTokenGenerator
     */
    private $tokenGenerator;


    /** emailGateway
     *
     *
     *
     * @var IEmailGateway
     */
    private $emailGateway;

    /** applicationConfiguration
     *
     *
     *
     * @var ApplicationConfig
     */
    private $applicationConfig;

    /** emailTemplate
     *
     *
     *
     * @var EmailTemplate
     */
    private $emailTemplate;

    /** recaptcha
     *
     *
     *
     * @var ReCaptcha
     */
    private $recaptcha;

    /** timeSeriesIngestor
     *
     *
     *
     * @var TimeSeriesIngester
     */
    private $timeSeriesIngestor;

    /** __construct
     *
     *  Constructor
     *
     * @param EntityManager $entityManager
     * @param CryptoTokenGenerator $tokenGenerator
     * @param IEmailGateway $emailGateway
     * @param ApplicationConfig $applicationConfig
     * @param EmailTemplate $emailTemplate
     * @param ReCaptcha $recaptcha
     * @param TimeSeriesIngester $timeSeriesIngestor
     */
    public function __construct(EntityManager $entityManager, CryptoTokenGenerator $tokenGenerator, IEmailGateway $emailGateway, ApplicationConfig $applicationConfig, EmailTemplate $emailTemplate, ReCaptcha $recaptcha, TimeSeriesIngester $timeSeriesIngestor) {
        $this->entityManager = $entityManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->emailGateway = $emailGateway;
        $this->applicationConfig = $applicationConfig;
        $this->emailTemplate = $emailTemplate;
        $this->recaptcha = $recaptcha;
        $this->timeSeriesIngestor = $timeSeriesIngestor;
    }

    /** newRequest
     *
     *  A new BulkUploadRequests should be created.
     *  A token should be created generated and saved along with the form data.
     *  An approved at timestamp will allow us to show the token has not been approved yet (will be set to null).
     *
     * @param INewBulkUploadRequestRequest $request
     */
    public function newRequest(INewBulkUploadRequestRequest $request) {
        $token = $this->tokenGenerator->generateToken(128);
        $bulkUploadRequest = new BulkUploadRequest(
            $request->getName(),
            $request->getEmailAddress(),
            $request->getOrganisation(),
            $request->getDescription(),
            $token
        );
        $this->entityManager->persist($bulkUploadRequest);
        $this->entityManager->flush();
        $this->entityManager->refresh($bulkUploadRequest);

        $this->emailGateway->sendEmail(
			$this->applicationConfig->get("default_sender_email_address"),
			'New bulk upload request',
			'A user has submitted a new bulk upload request.'
		);
    }

    /** approveRequest
     *
     *  Ensure it is not already approved.
     *  Update the approvedAt timestamp to the current time.
     *  Send email to email address associated with request, providing link to bulk upload form (with previously created token).
     *
     * @param BulkUploadRequest $bulkUploadRequest
     * @throws EInvalidInputs
     */
    public function approveRequest(BulkUploadRequest $bulkUploadRequest) {
        if ($bulkUploadRequest->getApprovedAt() !== null) {
            Throw new EInvalidInputs("Bulk upload request already approved");
        }

        $bulkUploadRequest->setApprovedAt(new \DateTime());

        $bulkUploadUrl = $this->applicationConfig->get("bulk_upload_url")
            . "?token=" . $bulkUploadRequest->getApprovalToken();

        $maxTotalBulkUploadSize = $this->applicationConfig->get("max_total_bulk_upload_size");

        $templatePath = ROOT_PATH . "/private/templates/bulk-upload-request-approved.html";
        $content = $this->emailTemplate->generateTemplate($templatePath, [
            "name" => $bulkUploadRequest->getName(),
            "bulkUploadUrl" => $bulkUploadUrl,
            "maxTotalBulkUploadSize" => $maxTotalBulkUploadSize,
        ]);

        // send email with token link
        $this->emailGateway->sendEmail(
            $bulkUploadRequest->getEmailAddress(),
            "Your bulk upload request has been approved",
            $content
        );

        $bulkUploadRequest->setStatus(BulkUploadRequest::STATUS_APPROVED);
        $this->entityManager->persist($bulkUploadRequest);
        $this->entityManager->flush();
    }

    /** denyRequest
     *
     *  Ensure it is not already approved.
     *  Send email to email address associated with request, notifying that the request has been denied.
     *  We update the bulk upload request and mark it as rejected.
     *
     * @param BulkUploadRequest $bulkUploadRequest
     * @throws EInvalidInputs|\Exception
     */
    public function denyRequest(BulkUploadRequest $bulkUploadRequest) {
        if ($bulkUploadRequest->getApprovedAt() !== null) {
            Throw new EInvalidInputs("Bulk upload request already approved");
        }

        $templatePath = ROOT_PATH . "/private/templates/bulk-upload-request-denied.html";
        $content = $this->emailTemplate->generateTemplate($templatePath, [
            "name" => $bulkUploadRequest->getName(),
            "requestDate" => $bulkUploadRequest->timestampCreated()->format("Y-m-d H:i"),
        ]);

        // send email with token link
        $this->emailGateway->sendEmail(
            $bulkUploadRequest->getEmailAddress(),
            "Your bulk upload request has been rejected",
            $content
        );

		$bulkUploadRequest->setStatus(BulkUploadRequest::STATUS_REJECTED);
        $this->entityManager->persist($bulkUploadRequest);
        $this->entityManager->flush();
    }

    /** getExchangeToken
     *
     *  Use Google's ReCaptcha library to check the validity of the recaptcha response code.
     *  Find the BulkUploadRequest from the provided approval token.
     *  Return a new exchangeToken.
     *  The exchange token will be sent with each upload to prove the uploader is a human.
     *
     * @param INotARobotRequest $request
     * @return string
     * @throws EInvalidInputs
     */
    public function getExchangeToken(INotARobotRequest $request) {
        $bulkUploadRequestRepository = $this->entityManager->getRepository(BulkUploadRequest::class);
        $criteria = new Criteria(new Comparison("approvalToken", "=", $request->getApprovalToken()));
        $oneWeekAgo = Carbon::now()->subWeek()->startOfDay();
        $criteria->andWhere(new Comparison("approvedAt", ">", $oneWeekAgo));
        /** @var BulkUploadRequest $bulkUploadRequest */
        $bulkUploadRequest = $bulkUploadRequestRepository->matching($criteria)->first();

        if (! $bulkUploadRequest) {
            Throw new EInvalidInputs("approvalToken expired or invalid");
        }

        $resp = $this->recaptcha->verify($request->getRecaptchaResponseCode(), $_SERVER['REMOTE_ADDR']);
        if (! $resp->isSuccess()) {
            Throw new EInvalidInputs("recaptchaResponseCode invalid");
        }

        $exchangeToken = $this->tokenGenerator->generateToken(128);

        $bulkUploadRequest->setExchangeToken($exchangeToken);
        $this->entityManager->persist($bulkUploadRequest);
        $this->entityManager->flush();

        return $exchangeToken;
    }

    /** uploadFile
     *
     *
     *
     * @param IBulkUploadRequest $request
     * @return BulkUploadedTimeSeries
     * @throws EEmptyTimeSeries
     * @throws EInvalidInputs
     */
    public function uploadFile(IBulkUploadRequest $request) {
        $bulkUploadRequestRepository = $this->entityManager->getRepository(BulkUploadRequest::class);
        $criteria = new Criteria(new Comparison("approvalToken", "=", $request->getApprovalToken()));
        $criteria->andWhere(new Comparison("exchangeToken", "=", $request->getExchangeToken()));
        $oneWeekAgo = Carbon::now()->subWeek()->startOfDay();
        $criteria->andWhere(new Comparison("approvedAt", ">", $oneWeekAgo));
        /** @var BulkUploadRequest $bulkUploadRequest */
        $bulkUploadRequest = $bulkUploadRequestRepository->matching($criteria)->first();

        if (! $bulkUploadRequest) {
            Throw new EInvalidInputs("Bulk upload link has expired. Please submit a new request to upload.");
        }

        $file = $request->getFile();
        $tempName = $file["tmp_name"];
        $realName = $file["name"];
        $newName = pathinfo($tempName, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($tempName, PATHINFO_FILENAME) .
            "." . pathinfo($realName, PATHINFO_EXTENSION);

        rename($tempName, $newName);

        $this->checkTotalBulkUploadSize($bulkUploadRequest, $file["size"]);

        try {
            $dataPoints = $this->timeSeriesIngestor->getDataPoints($newName);
        } catch (EUnsupportedFileExtension $e) {
	        throw new EInvalidInputs("File format is not accepted");
        } catch (ESoxError $e) {
            throw new EInvalidInputs("Audio file encoding not supported");
        }

        if (0 === count($dataPoints)) {
            throw new EEmptyTimeSeries();
        }

        $timeSeries = new BulkUploadedTimeSeries($dataPoints, $file["size"]);

        $fileName = pathinfo($realName, PATHINFO_FILENAME);
        $fileName = $this->applyNextFileNameIndex($fileName);
        $timeSeries->setName($fileName);

        $timeSeries->setBulkUploadRequest($bulkUploadRequest);

        $this->entityManager->persist($timeSeries);
        $this->entityManager->flush();
        $this->entityManager->refresh($timeSeries);

        return $timeSeries;
    }

    private function applyNextFileNameIndex($fileName) {
        $currentFileNameIndex = $this->getCurrentFileNameIndex($fileName);
        return $fileName . "_" . ++$currentFileNameIndex;
    }

    private function getCurrentFileNameIndex($fileName) {
        $currentFileNameIndexFromBulkTableQuery = $this->entityManager->createQuery("
			select MAX(INT(REPLACE(b.name, CONCAT(:fileName, '', '_'), ''))) current_index 
			from " . BulkUploadedTimeSeries::class . " b 
			where REGEXP(b.name, CONCAT('^', :fileName, '_[0-9]+$')) = true
		");
	    $currentFileNameIndexFromTimeseriesTableQuery = $this->entityManager->createQuery("
			select MAX(INT(REPLACE(t.name, CONCAT(:fileName, '', '_'), ''))) current_index 
			from " . PersistedTimeSeries::class . " t
			where REGEXP(t.name, CONCAT('^', :fileName, '_[0-9]+$')) = true
		");
        $bulkIndexResult = $currentFileNameIndexFromBulkTableQuery->setParameter("fileName", $fileName)->execute();
	    $timeseriesIndexResult = $currentFileNameIndexFromTimeseriesTableQuery->setParameter("fileName", $fileName)->execute();

        return max([$bulkIndexResult[0]["current_index"], $timeseriesIndexResult[0]["current_index"]]);
    }

    /** checkTotalBulkUploadSize
     *
     *  Throw error if bulk upload size will be exceeded with new file being uploaded.
     *
     * @param BulkUploadRequest $bulkUploadRequest
     * @param $addSize
     * @throws EInvalidInputs
     */
    private function checkTotalBulkUploadSize($bulkUploadRequest, $addSize) {
        // $maxSize = 2*1000*1000; // 10*1000*1000*1000; // 10GB
        $maxSize = (int) $this->applicationConfig->get("max_total_bulk_upload_size");
        $humanMaxSize = $this->humanFilesize($maxSize, 0);
        if ($addSize + $this->getCurrentBulkUploadSize($bulkUploadRequest) > $maxSize) {
            Throw new EInvalidInputs("Total bulk upload size is too large. Must not exceed $humanMaxSize.");
        }
    }

    private function humanFilesize($bytes, $decimals = 2) {
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

    /** getCurrentBulkUploadSize
     *
     *  Return the current bulk upload size. Sum of file sizes uploaded up to this point
     *
     * @param BulkUploadRequest $bulkUploadRequest
     * @return int bytes
     */
    private function getCurrentBulkUploadSize($bulkUploadRequest) {
        $result = $this->entityManager->createQuery("SELECT SUM(b.fileSize) totalFileSize FROM " . BulkUploadedTimeSeries::class . " b WHERE b.isSubmitted = false AND b.bulkUploadRequest = " . $bulkUploadRequest->getId())->execute();
        return $result[0]["totalFileSize"];
    }

    /** submitBulkUpload
     *
     *
     *
     * @param ISubmitBulkUploadRequest $request
     * @throws EInvalidInputs
     */
    public function submitBulkUpload(ISubmitBulkUploadRequest $request) {
        $bulkUploadRequestRepository = $this->entityManager->getRepository(BulkUploadRequest::class);
        $criteria = new Criteria(new Comparison("approvalToken", "=", $request->getApprovalToken()));
        $criteria->andWhere(new Comparison("exchangeToken", "=", $request->getExchangeToken()));
        $oneWeekAgo = Carbon::now()->subWeek()->startOfDay();
        $criteria->andWhere(new Comparison("approvedAt", ">", $oneWeekAgo));
        /** @var BulkUploadRequest $bulkUploadRequest */
        $bulkUploadRequest = $bulkUploadRequestRepository->matching($criteria)->first();

        if (! $bulkUploadRequest) {
            Throw new EInvalidInputs("Bulk upload link has expired. Please submit a new request to upload.");
        }

        /**
         * Be nice, and remove duplicate Id's to minimize later troubles.
         */
        $timeSeriesIds = array_unique($request->getTimeSeries());

        /**
         * Check that all the time series ids exist and belong to this bulk upload, before applying meta data and submitting.
         * Check none have already been submitted.
         */
        $timeSeries = array_map(function ($timeSeriesId) use ($bulkUploadRequest) {
            $timeSeries = $this->entityManager->getRepository(BulkUploadedTimeSeries::class)->findBy([
                "bulkUploadRequest" => $bulkUploadRequest,
                "id" => $timeSeriesId,
            ]);
            if (0 === count($timeSeries)) {
                Throw new EInvalidInputs("Time series id " . $timeSeriesId . " not found for this bulk upload");
            }
            /** @var BulkUploadedTimeSeries $timeSeries */
            $timeSeries = $timeSeries[0];
            if ($timeSeries->isSubmitted()) {
                Throw new EInvalidInputs("Time series id " . $timeSeriesId . " has already been submitted");
            }
            return $timeSeries;
        }, $timeSeriesIds);

        $contributor = $this->entityManager->getRepository(Contributor::class)->findOneBy(["emailAddress" => $timeSeries[0]->getBulkUploadRequest()->getEmailAddress()]);
        $contributor = $contributor ?: new Contributor($timeSeries[0]->getBulkUploadRequest()->getName(), $timeSeries[0]->getBulkUploadRequest()->getEmailAddress());

        if (null !== $contributor){
        	$contributor->setWantsAggregationEmail($request->getWantsAggregationEmail());
		}

        $tags = $request->getMetadataTags()->toArray();
        array_walk($tags, function ($tag) {
            $this->entityManager->persist($tag);
        });

        /**
         * Apply meta data to each BulkUploadedTimeSeries and mark as submitted.
         */
        array_walk($timeSeries, function (BulkUploadedTimeSeries $timeSeries) use ($request, $bulkUploadRequest, $contributor, $tags) {
            $timeSeries->setCategory($request->getMetadataCategory());
            $timeSeries->setTags(new ArrayCollection($tags));
            $timeSeries->setSamplingInformation(SamplingInformation::defined(
                $request->getMetadataSamplingRate(),
                $request->getMetadataSamplingUnit()
            ));
            if ($request->hasMetadataRootWord() && ($rootWord = $request->getMetadataRootWord())) {
                $originalFileName = preg_replace("/(_\d+)$/", "", $timeSeries->getName());
                $newFileName = $this->applyNextFileNameIndex($rootWord . "_" . $originalFileName);
                $timeSeries->setName($newFileName);
            }

			$timeSeries->setContributor($contributor);
            $timeSeries->setAsSubmitted();

			$this->entityManager->persist($contributor);
            $this->entityManager->persist($timeSeries);
        });

        $this->entityManager->flush();
    }
}
