<?php

namespace PresentationLayer\Routes\Timeseries\Search\Post;

use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\SearchService\SearchService;
use DomainLayer\TimeSeriesManagement\TimeSeriesRenderer\TimeSeriesRenderer;
use PresentationLayer\Routes\Timeseries\Search\SearchWebRequest;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\Timeseries\Search\Post
 */
class Post extends AbstractRoute
{
    /** searchService
     *
     *
     *
     * @var SearchService
     */
    private $searchService;

    /** searchWebRequest
     *
     *
     *
     * @var SearchWebRequest
     */
    private $searchWebRequest;

    /** timeSeriesRenderer
     *
     *
     *
     * @var TimeSeriesRenderer
     */
    private $timeSeriesRenderer;

    /** __construct
     *
     *  Constructor
     *
     * @param SearchService $searchService
     * @param SearchWebRequest $searchWebRequest
     * @param TimeSeriesRenderer $timeSeriesRenderer
     */
    public function __construct(SearchService $searchService, SearchWebRequest $searchWebRequest, TimeSeriesRenderer $timeSeriesRenderer) {
        $this->searchService = $searchService;
        $this->searchWebRequest = $searchWebRequest;
        $this->timeSeriesRenderer = $timeSeriesRenderer;
    }

    public function execute() {
        /**
         * Search
         */
        $time1 = microtime(true);
        $this->searchWebRequest->populate($this->request->getBodyAsArray());
        $search = $this->searchService->search($this->searchWebRequest);
        $time2 = microtime(true);

        /**
         * Render
         */
        $timeSeries = array_map(function (PersistedTimeSeries $timeSeries) {
            return $this->timeSeriesRenderer->renderSimple($timeSeries);
        }, $search["items"]);

        $this->response->setReturnBody(new JSONBody([
            "timeSeries" => $timeSeries,
            "total" => $search["total"],
            "time" => round($time2 - $time1, 3),
            "pageSize" => $search["pageSize"],
            "searchingBy" => [
                "type" => $this->searchWebRequest->getSearchType(),
                "match" => $this->searchWebRequest->getMatch(),
            ],
        ]));
    }
}
