<?php

namespace PresentationLayer\Routes\Timeseries\Slug\Get;

use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\ORM\Source\Repository\ISourceRepository;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository\ITimeSeriesRepository;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery\CategoryFilter\CategoryFilter;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery\SearchQuery;
use DomainLayer\TimeSeriesManagement\TimeSeriesRenderer\TimeSeriesRenderer;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\Timeseries\SearchQueryFactory;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Timeseries\Slug\Get
 */
class Get extends AbstractRoute{

    /** ITimeSeriesRepository
     *
     *  Access to the time series repository.
     *
     * @var ITimeSeriesRepository
     */
    private $timeSeriesRepository;

    /** $timeSeriesRenderer
     *
     *  A service used to render a TimeSeries object into a JSON array
     *  compatible with the front end.
     *
     * @var TimeSeriesRenderer
     */
    private $timeSeriesRenderer;

    private $siteAttributeRepository;

    private $sourceRepository;

    private $searchQueryFactory;

    private function constructSearchQuery($getParams){
		return $this->searchQueryFactory->constructSearchQuery($getParams, 20);
    }

    /** __construct
     *
     *  Get constructor.
     *
     * @param ITimeSeriesRepository $timeSeriesRepository
     * @param TimeSeriesRenderer $timeSeriesRenderer
     */
    public function __construct(ITimeSeriesRepository $timeSeriesRepository, TimeSeriesRenderer $timeSeriesRenderer,
        ISiteAttributeRepository $siteAttributeRepository, ISourceRepository $sourceRepository,
		SearchQueryFactory $searchQueryFactory){

        $this->timeSeriesRepository = $timeSeriesRepository;
        $this->timeSeriesRenderer = $timeSeriesRenderer;
        $this->siteAttributeRepository = $siteAttributeRepository;
        $this->sourceRepository = $sourceRepository;
        $this->searchQueryFactory = $searchQueryFactory;
    }

    /** execute
     *
     *  Route execution.
     *
     */
    public function execute(){
        $timeSeriesId = $this->queryParams[0];
        $timeSeries = $this->timeSeriesRepository->findById($timeSeriesId);

        if (NULL === $timeSeries){
            throw new EInvalidInputs("Invalid timeseries ID '$timeSeriesId'.");
        }

        if (isset($_GET["noNeighbours"]) && TRUE == $_GET["noNeighbours"]){
            $response = $this->timeSeriesRenderer->renderTimeSeriesBriefly($timeSeries);
        }else{
            $searchQuery = $this->constructSearchQuery($_GET);
            $response = $this->timeSeriesRenderer->renderTimeSeries($timeSeries, $searchQuery);
        }

        $this->response->setReturnBody(new JSONBody($response));
    }

}