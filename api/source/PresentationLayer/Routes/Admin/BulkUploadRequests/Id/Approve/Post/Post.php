<?php

namespace PresentationLayer\Routes\Admin\BulkUploadRequests\Id\Approve\Post;

use Doctrine\ORM\EntityManager;
use DomainLayer\BulkUploadRequestService\BulkUploadRequestService;
use DomainLayer\ORM\BulkUploadRequest\BulkUploadRequest;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\Admin\BulkUploadRequests\Id\Approve\Post
 */
class Post extends UserInferredRoute
{

    /** bulkUploadRequestService
     *
     *
     *
     * @var BulkUploadRequestService
     */
    private $bulkUploadRequestService;

	/**
	 * Post constructor.
	 * @param SessionService $sessionService
	 * @param EntityManager $entityManager
	 * @param BulkUploadRequestService $bulkUploadRequestService
	 */
    public function __construct(SessionService $sessionService, EntityManager $entityManager, BulkUploadRequestService $bulkUploadRequestService) {
        parent::__construct($sessionService, $entityManager);
        $this->bulkUploadRequestService = $bulkUploadRequestService;
    }

    public function execute() {
		parent::execute();

		/** @var BulkUploadRequest $bulkUploadRequest */
        $bulkUploadRequest = $this->entityManager->find(BulkUploadRequest::class, $this->queryParams[0]);

        if (is_null($bulkUploadRequest)) {
            Throw new EInvalidInputs("Bulk upload request not found");
        }

        $this->bulkUploadRequestService->approveRequest($bulkUploadRequest);

        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
