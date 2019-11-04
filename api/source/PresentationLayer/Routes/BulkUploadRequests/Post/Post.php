<?php

namespace PresentationLayer\Routes\BulkUploadRequests\Post;

use DomainLayer\BulkUploadRequestService\BulkUploadRequestService;
use PresentationLayer\Routes\BulkUploadRequests\NewBulkUploadRequestWebRequest;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\BulkUploadRequests\Post
 */
class Post extends AbstractRoute
{
    /** newBulkUploadRequestWebRequest
     *
     *
     *
     * @var NewBulkUploadRequestWebRequest
     */
    private $newBulkUploadRequestWebRequest;

    /** newBulkUploadRequestService
     *
     *
     *
     * @var BulkUploadRequestService
     */
    private $bulkUploadRequestService;

    /** __construct
     *
     *  Constructor
     *
     * @param NewBulkUploadRequestWebRequest $newBulkUploadRequestWebRequest
     * @param BulkUploadRequestService $bulkUploadRequestService
     */
    public function __construct(NewBulkUploadRequestWebRequest $newBulkUploadRequestWebRequest, BulkUploadRequestService $bulkUploadRequestService) {
        $this->newBulkUploadRequestWebRequest = $newBulkUploadRequestWebRequest;
        $this->bulkUploadRequestService = $bulkUploadRequestService;
    }

    public function execute() {
        $this->newBulkUploadRequestWebRequest->fill($this->request->getBodyAsArray());
        $this->bulkUploadRequestService->newRequest($this->newBulkUploadRequestWebRequest);
        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
