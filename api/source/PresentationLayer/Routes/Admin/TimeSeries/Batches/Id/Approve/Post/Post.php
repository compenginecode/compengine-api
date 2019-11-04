<?php

namespace PresentationLayer\Routes\Admin\TimeSeries\Batches\Id\Approve\Post;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\BulkUploadRequest\BulkUploadRequest;
use DomainLayer\ORM\TimeSeries\BulkUploadedTimeSeries\BulkUploadedTimeSeries;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\Admin\TimeSeries\Batches\Id\Approve\Post
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

        $bulkUploadedTimeSeries = $this->entityManager->getRepository(BulkUploadedTimeSeries::class)->findBy(compact('bulkUploadRequest'));

        array_walk($bulkUploadedTimeSeries, function (BulkUploadedTimeSeries $bulkUploadedTimeSeries) {
            if($bulkUploadedTimeSeries->isPendingApproval()) {
                $bulkUploadedTimeSeries->setAsApproved();
            }
        });

        $this->entityManager->flush();

        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
