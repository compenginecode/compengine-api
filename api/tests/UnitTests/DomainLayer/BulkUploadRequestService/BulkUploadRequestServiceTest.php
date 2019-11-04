<?php

namespace UnitTests\DomainLayer\BulkUploadRequestService;

use ConfigurationLayer\ApplicationConfig\ApplicationConfig;
use Doctrine\ORM\EntityManager;
use DomainLayer\BulkUploadRequestService\BulkUploadRequestService;
use DomainLayer\ORM\BulkUploadRequest\BulkUploadRequest;
use DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester\TimeSeriesIngester;
use InfrastructureLayer\Crypto\TokenGenerator\CryptoTokenGenerator\CryptoTokenGenerator;
use InfrastructureLayer\EmailGateway\IEmailGateway;
use InfrastructureLayer\EmailTemplate\EmailTemplate;
use Mockery\MockInterface;
use PresentationLayer\Routes\BulkUploadRequests\NewBulkUploadRequestWebRequest;
use PresentationLayer\Routes\EInvalidInputs;
use ReCaptcha\ReCaptcha;

/**
 * Class BulkUploadRequestServiceTest
 * @package UnitTests\DomainLayer\BulkUploadRequestService
 */
class BulkUploadRequestServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * bulkUploadRequestService
     *
     *
     *
     * @var BulkUploadRequestService
     */
    private $bulkUploadRequestService;

    /** entityManager
     *
     *
     *
     * @var EntityManager|MockInterface
     */
    private $entityManager;

    /** tokenGenerator
     *
     *
     *
     * @var CryptoTokenGenerator|MockInterface
     */
    private $tokenGenerator;


    /** emailGateway
     *
     *
     *
     * @var IEmailGateway|MockInterface
     */
    private $emailGateway;

    /** applicationConfiguration
     *
     *
     *
     * @var ApplicationConfig|MockInterface
     */
    private $applicationConfig;

    /** emailTemplate
     *
     *
     *
     * @var EmailTemplate|MockInterface
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
     * @var TimeSeriesIngester|MockInterface
     */
    private $timeSeriesIngestor;

    /** tearDown
     *
     *  Closes and tests all Mockery assertions.
     *
     */
    public function tearDown(){
        \Mockery::close();
    }

    /** setUp
     *
     *  Set up each test
     *
     */
    public function setUp() {
        $this->entityManager = \Mockery::mock(EntityManager::class);
        $this->entityManager->shouldReceive("refresh");
        $this->tokenGenerator = \Mockery::mock(CryptoTokenGenerator::class);
        $this->emailGateway = \Mockery::mock(IEmailGateway::class);
        $this->applicationConfig = \Mockery::mock(ApplicationConfig::class);
        $this->applicationConfig->shouldReceive("get");
        $this->emailTemplate = \Mockery::mock(EmailTemplate::class);
        $this->emailTemplate->shouldReceive("generateTemplate");
        $this->recaptcha = \Mockery::mock(ReCaptcha::class);
        $this->timeSeriesIngestor = \Mockery::mock(TimeSeriesIngester::class);
        $this->bulkUploadRequestService = new BulkUploadRequestService(
            $this->entityManager, $this->tokenGenerator, $this->emailGateway, $this->applicationConfig, $this->emailTemplate, $this->recaptcha, $this->timeSeriesIngestor
        );
    }

    /** test_processing_a_new_bulk_upload_request
     *
     *  A new BulkUploadRequests should be created - Check em persist + flush are called once.
     *  A token should be created generated and saved along with the form data - Check token generator is called once
     *  An approved at timestamp will allow us to show the token has not been approved yet (will be set to null).
     *
     */
    public function test_processing_a_new_bulk_upload_request() {
        $request = new NewBulkUploadRequestWebRequest();
        $request->fill([
            "name" => "Jake Finn",
            "emailAddress" => "jake.finn@example.org",
            "organisation" => "Bubblegum Enterprises",
            "description" => "A plot of the Ice King's moods over time.",
        ]);
        $this->tokenGenerator->shouldReceive("generateToken")->once();
        $this->entityManager->shouldReceive(["persist" => null, "flush" => null])->once();
        $this->bulkUploadRequestService->newRequest($request);
    }

    /** test_approving_a_bulk_upload_request
     *
     *  Ensure it is not already approved.
     *  Update the approvedAt timestamp to the current time.
     *  Send email to email address associated with request, providing link to bulk upload form (with previously created token).
     *
     */
    public function test_approving_a_bulk_upload_request() {
        /** @var BulkUploadRequest|MockInterface $bulkUploadRequest */
        $bulkUploadRequest = \Mockery::mock(BulkUploadRequest::class);
        $bulkUploadRequest->makePartial()->shouldReceive(['setApprovedAt' => null])->once();
        $this->entityManager->shouldReceive(["persist" => null, "flush" => null])->once();
        $this->emailGateway->shouldReceive(["sendEmail" => null])->once();
        $this->bulkUploadRequestService->approveRequest($bulkUploadRequest);
    }

    /** test_approving_an_already_approved_bulk_upload_request
     *
     *  Ensure it is not already approved. - set approvedAt date before approving, expect exception
     *
     */
    public function test_approving_an_already_approved_bulk_upload_request() {
        /** @var BulkUploadRequest|MockInterface $bulkUploadRequest */
        $bulkUploadRequest = \Mockery::mock(BulkUploadRequest::class);
        $bulkUploadRequest->makePartial()->shouldReceive(["getApprovedAt" => new \DateTime()])->once();
        $this->setExpectedException(EInvalidInputs::class, "Bulk upload request already approved");
        $this->entityManager->shouldNotReceive(["persist", "flush"]);
        $this->bulkUploadRequestService->approveRequest($bulkUploadRequest);
    }

    /** test_denying_a_bulk_upload_request
     *
     *  Ensure it is not already approved.
     *  Send email to email address associated with request, notifying that the request has been denied.
     *  Delete the bulk upload request.
     *
     */
    public function test_denying_a_bulk_upload_request() {
        /** @var BulkUploadRequest|MockInterface $bulkUploadRequest */
        $bulkUploadRequest = \Mockery::mock(BulkUploadRequest::class);
        $bulkUploadRequest->makePartial()->shouldReceive(["timestampCreated" => new \DateTime()]);
        $this->entityManager->shouldReceive(["remove" => null, "flush" => null])->once();
        $this->emailGateway->shouldReceive(["sendEmail" => null])->once();
        $this->bulkUploadRequestService->denyRequest($bulkUploadRequest);
    }
}
