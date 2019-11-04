<?php

namespace PresentationLayer\Routes\BulkUploadRequests\NotARobot\Post;

use DomainLayer\BulkUploadRequestService\BulkUploadRequestService;
use PresentationLayer\Routes\BulkUploadRequests\NotARobotWebRequest;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\BulkUploadRequests\NotARobot\Post
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

    /** notARobotWebRequest
     *
     *
     *
     * @var NotARobotWebRequest
     */
    private $notARobotWebRequest;

    /** __construct
     *
     *  Constructor
     *
     * @param BulkUploadRequestService $bulkUploadRequestService
     * @param NotARobotWebRequest $notARobotWebRequest
     */
    public function __construct(BulkUploadRequestService $bulkUploadRequestService, NotARobotWebRequest $notARobotWebRequest) {
        $this->bulkUploadRequestService = $bulkUploadRequestService;
        $this->notARobotWebRequest = $notARobotWebRequest;
    }

    public function execute() {
        $this->notARobotWebRequest->fill($this->request->getBodyAsArray());
        $exchangeToken = $this->bulkUploadRequestService->getExchangeToken($this->notARobotWebRequest);
        $this->response->setReturnBody(new JSONBody(compact("exchangeToken")));
    }
}
