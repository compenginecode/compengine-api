<?php

namespace PresentationLayer\Routes\Admin\BulkUploadRequests\Get;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\BulkUploadRequest\BulkUploadRequest;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Admin\BulkUploadRequests
 */
class Get extends UserInferredRoute
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

        $bulkUploadRequestRepository = $this->entityManager->getRepository(BulkUploadRequest::class);
        $newRequests = $bulkUploadRequestRepository->findAll();
        $newRequests = array_map(function (BulkUploadRequest $bulkUploadRequest) {
            return $this->renderBulkUploadRequest($bulkUploadRequest);
        }, $newRequests);

        $this->response->setReturnBody(new JSONBody(compact("newRequests")));
    }

    /** renderBulkUploadRequest
     *
     *
     *
     * @param BulkUploadRequest $bulkUploadRequest
     * @return array
     */
    private function renderBulkUploadRequest(BulkUploadRequest $bulkUploadRequest) {
        return [
            "id" => $bulkUploadRequest->getId(),
            "name" => $bulkUploadRequest->getName(),
            "emailAddress" => $bulkUploadRequest->getEmailAddress(),
            "organisation" => $bulkUploadRequest->getOrganisation(),
            "description" => $bulkUploadRequest->getDescription(),
            "createdAt" => $bulkUploadRequest->timestampCreated()->format("Y-m-d H:i:s"),
			"status" => $bulkUploadRequest->getStatus()
        ];
    }
}
