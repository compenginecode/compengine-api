<?php

namespace PresentationLayer\Routes\Admin\TimeSeries\Batches\Id\Deny\Post;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\BulkUploadRequest\BulkUploadRequest;
use DomainLayer\ORM\Notification\Notification;
use DomainLayer\ORM\Tag\Tag;
use DomainLayer\ORM\TimeSeries\BulkUploadedTimeSeries\BulkUploadedTimeSeries;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\Admin\TimeSeries\Batches\Id\Deny\Post
 */
class Post extends UserInferredRoute
{

    /** __construct
     *
     *  Constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager) {
		parent::__construct($sessionService, $entityManager);
    }

    public function execute() {
		parent::execute();

        /** @var BulkUploadRequest $bulkUploadRequest */
        $bulkUploadRequest = $this->entityManager->find(BulkUploadRequest::class, $this->queryParams[0]);

        if (is_null($bulkUploadRequest)) {
            Throw new EInvalidInputs("Bulk upload batch does not exist");
        }

        /** @var BulkUploadedTimeSeries[] $bulkUploadedTimeSeries */
        $bulkUploadedTimeSeries = $this->entityManager->getRepository(BulkUploadedTimeSeries::class)->findBy(compact('bulkUploadRequest'));

        array_walk($bulkUploadedTimeSeries, function (BulkUploadedTimeSeries $bulkUploadedTimeSeries) {
            if($bulkUploadedTimeSeries->isPendingApproval()) {
            	$bulkUploadedTimeSeries->setAsDenied();
                $this->entityManager->persist($bulkUploadedTimeSeries);
            }
        });

        $requestBody = $this->request->getBodyAsArray();
        $reason = !empty($requestBody['reason']) ? $requestBody['reason'] : "Reason not provided";
        $fileName = $bulkUploadedTimeSeries[0]->getName() . (count($bulkUploadedTimeSeries) > 1 ? " and " . count($bulkUploadedTimeSeries) . " other" . (count($bulkUploadedTimeSeries) > 2 ? "s" : "") : "");

        $notification = new Notification(
            Notification::DAILY,
            $bulkUploadRequest->getName(),
            $bulkUploadRequest->getEmailAddress(),
            Notification::TIME_SERIES_DENIED,
            [
                "fileName" => $fileName,
                "count" => count($bulkUploadedTimeSeries),
                "reason" => $reason,
            ]
        );

        $this->entityManager->persist($notification);

        $this->entityManager->flush();

        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }

    /**
     * @param BulkUploadedTimeSeries[] $bulkUploadedTimeSeries
     * @return Tag[]
     */
    private function tagsToRemove($bulkUploadedTimeSeries) {
        if (0 === count($bulkUploadedTimeSeries)) {
            return [];
        }

        $numberInThisBatch = count($bulkUploadedTimeSeries);

        return array_filter($bulkUploadedTimeSeries[0]->getTags()->toArray(), function (Tag $tag) use ($numberInThisBatch) {
            if ($tag->getApprovalStatus()->equals(ApprovalStatus::APPROVED)) {
                return false;
            }

            $numberOfPersistedTimeSeriesUsingThisTag = (int) $this->entityManager->getRepository(PersistedTimeSeries::class)
                ->createQueryBuilder('ts')->select('COUNT(ts.id)')
                ->where(':tag MEMBER OF ts.tags')->setParameter('tag', $tag)
                ->getQuery()->getSingleScalarResult();

            if ($numberOfPersistedTimeSeriesUsingThisTag > 0) {
                return false;
            }

            $numberOfBulkUploadedTimeSeriesUsingThisTag = (int) $this->entityManager->getRepository(BulkUploadedTimeSeries::class)
                ->createQueryBuilder('ts')->select('COUNT(ts.id)')
                ->where(':tag MEMBER OF ts.tags')->setParameter('tag', $tag)
                ->getQuery()->getSingleScalarResult();

            if ($numberOfBulkUploadedTimeSeriesUsingThisTag > $numberInThisBatch) {
                return false;
            }

            return true;
        });
    }

}
