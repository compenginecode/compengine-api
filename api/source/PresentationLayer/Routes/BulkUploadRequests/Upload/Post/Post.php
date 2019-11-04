<?php

namespace PresentationLayer\Routes\BulkUploadRequests\Upload\Post;

use DomainLayer\BulkUploadRequestService\BulkUploadRequestService;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonService\Exceptions\EEmptyTimeSeries;
use DomainLayer\TimeSeriesManagement\Ingestion\Converters\Exceptions\EParseConversionError;
use PresentationLayer\Routes\BulkUploadRequests\BulkUploadWebRequest;
use PresentationLayer\Routing\StatusCode\UnprocessableEntity;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\BulkUploadRequests\Upload\Post
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

    /** bulkUploadWebRequest
     *
     *
     *
     * @var BulkUploadWebRequest
     */
    private $bulkUploadWebRequest;

    /** __construct
     *
     *  Constructor
     *
     * @param BulkUploadRequestService $bulkUploadRequestService
     * @param BulkUploadWebRequest $bulkUploadWebRequest
     */
    public function __construct(BulkUploadRequestService $bulkUploadRequestService, BulkUploadWebRequest $bulkUploadWebRequest) {
        $this->bulkUploadRequestService = $bulkUploadRequestService;
        $this->bulkUploadWebRequest = $bulkUploadWebRequest;
    }

    public function execute() {
        $this->bulkUploadWebRequest->fill($this->request->getPostData());

        try {
            $timeSeries = $this->bulkUploadRequestService->uploadFile($this->bulkUploadWebRequest);

            $this->response->setReturnBody(new JSONBody([
                "timeSeriesId" => $timeSeries->getId(),
            ]));
        }

        catch (EParseConversionError $exception) {
            $this->response->setStatusCode(new UnprocessableEntity());
            $this->response->setReturnBody(new JSONBody([
                "message" => "Could not detect the delimiter."
            ]));
        }

        catch (EEmptyTimeSeries $exception) {
            $this->response->setStatusCode(new UnprocessableEntity());
            $this->response->setReturnBody(new JSONBody([
                "message" => "Time series is empty."
            ]));
        }

        catch (\Exception $exception) {
            throw $exception;
        }
    }
}
