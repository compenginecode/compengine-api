<?php

namespace PresentationLayer\Routes\BulkUploadRequests\Submit\Post;

use DomainLayer\BulkUploadRequestService\BulkUploadRequestService;
use PresentationLayer\Routes\BulkUploadRequests\SubmitBulkUploadRequest;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\BulkUploadRequests\Submit\Post
 */
class Post extends AbstractRoute
{
    /** bulkUploadRequestService
     *
     *
     *
     * @var BulkUploadRequestService
     */
    private $bulkUploadRequestService;

    /** submitBulkUploadWebRequest
     *
     *
     *
     * @var SubmitBulkUploadRequest
     */
    private $submitBulkUploadWebRequest;

    /** __construct
     *
     *  Constructor
     *
     * @param BulkUploadRequestService $bulkUploadRequestService
     * @param SubmitBulkUploadRequest $submitBulkUploadWebRequest
     */
    public function __construct(BulkUploadRequestService $bulkUploadRequestService, SubmitBulkUploadRequest $submitBulkUploadWebRequest) {
        $this->bulkUploadRequestService = $bulkUploadRequestService;
        $this->submitBulkUploadWebRequest = $submitBulkUploadWebRequest;
    }

    public function execute() {
        $this->submitBulkUploadWebRequest->fill($this->request->getBodyAsArray());
        $this->bulkUploadRequestService->submitBulkUpload($this->submitBulkUploadWebRequest);

        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
