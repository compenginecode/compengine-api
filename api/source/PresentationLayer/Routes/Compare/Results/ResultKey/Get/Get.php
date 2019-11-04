<?php

namespace PresentationLayer\Routes\Compare\Results\ResultKey\Get;

use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\ORM\Source\Repository\ISourceRepository;
use DomainLayer\ORM\TimeSeries\IngestedTimeSeries\IngestedTimeSeries;
use DomainLayer\ORM\TopLevelCategory\TopLevelCategory;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonStore\ComparisonStore;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\NearestNeighbourService;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\Neighbourhood\Neighbourhood;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\NeighbourhoodRenderer\NeighbourhoodRenderer;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery\CategoryFilter\CategoryFilter;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery\SearchQuery;
use DomainLayer\TimeSeriesManagement\Downsampler\LTTBDownsampler;
use DomainLayer\TimeSeriesManagement\TimeSeriesRenderer\TimeSeriesRenderer;
use PresentationLayer\Routes\EInvalidInputs;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Compare\Results\ResultKey\Get
 */
class Get extends AbstractRoute{

    /** $comparisonStore
     *
     *  Store used to retrieve temporarily compared time series.
     *
     * @var ComparisonStore
     */
    private $comparisonStore;

    /** $neighbourhoodRenderer
     *
     *  Service used to render neighbourhood objects into JSON.
     *
     * @var NeighbourhoodRenderer
     */
    private $neighbourhoodRenderer;

    /**
     * @var NearestNeighbourService
     */
    private $nearestNeighbourService;

    private $siteAttributeRepository;

    private $LTTBDownsampler;

    private $sourceRepository;

	private $timeSeriesRenderer;

    private function constructSearchQuery($getParams){
        $category = CategoryFilter::any();
        if (isset($getParams["topLevelCategory"])){
            if (CategoryFilter::isValidOption($getParams["topLevelCategory"])){
                $category = CategoryFilter::byValue($getParams["topLevelCategory"]);
            }else{
                throw new EInvalidInputs("Invalid value for category. Must be one of: " .
                    implode(", ", CategoryFilter::options()));
            }
        }

        $tags = [];
        if (isset($getParams["tags"])){
            $tagsArray = explode(";", $getParams["tags"]);
            if (count($tagsArray) > 0){
                $tags = $tagsArray;
            }
        }

        $currentFVF = $this->siteAttributeRepository->getCurrentFeatureVectorFamily();
        if ($category->equals(CategoryFilter::FILTER_SYNTHETIC)){
            $index = $currentFVF->getSyntheticIndex();
        }else if ($category->equals(CategoryFilter::FILTER_REAL)){
            $index = $currentFVF->getRealIndex();
        }else{
            $index = $currentFVF->getCommonIndex();
        }

        $searchQuery = new SearchQuery($index);
        $searchQuery->setDirectNeighbourLimit(100);
        $searchQuery->setCategoryFilter($category);
        $searchQuery->setTags($tags);

        if (isset($getParams["source"])){
            $searchQuery->setSource($this->sourceRepository->findOneByName($getParams["source"]));
        }

        return $searchQuery;
    }

    /**
     * Get constructor.
     * @param ComparisonStore $comparisonStore
     * @param NearestNeighbourService $nearestNeighbourService
     * @param NeighbourhoodRenderer $neighbourhoodRenderer
     * @param ISiteAttributeRepository $siteAttributeRepository
     * @param LTTBDownsampler $LTTBDownsampler
     */
    public function __construct(ComparisonStore $comparisonStore, NearestNeighbourService $nearestNeighbourService,
        NeighbourhoodRenderer $neighbourhoodRenderer, ISiteAttributeRepository $siteAttributeRepository,
        LTTBDownsampler $LTTBDownsampler, ISourceRepository $sourceRepository, TimeSeriesRenderer $timeSeriesRenderer){

        $this->comparisonStore = $comparisonStore;
        $this->neighbourhoodRenderer = $neighbourhoodRenderer;
        $this->nearestNeighbourService = $nearestNeighbourService;
        $this->siteAttributeRepository = $siteAttributeRepository;
        $this->LTTBDownsampler = $LTTBDownsampler;
        $this->sourceRepository = $sourceRepository;
	    $this->timeSeriesRenderer = $timeSeriesRenderer;
    }

    /** execute
     *
     *  Route execution.
     *
     */
    public function execute(){
        $comparisonData = $this->comparisonStore->retrieveComparisonResult($this->queryParams[0]);
        if (NULL !== $comparisonData && FALSE !== $comparisonData){
            /** @var IngestedTimeSeries $ingestedTimeSeries */
            $ingestedTimeSeries = $comparisonData["timeSeries"];

            $searchQuery = $this->constructSearchQuery($_GET);
            $neighbourhood = $this->nearestNeighbourService->findNearestNeighbours(
                $ingestedTimeSeries->getNormalizedFeatureVector(),
                $searchQuery
            );

            $response = array(
                "timeSeries" => array(
                    "raw" => $this->LTTBDownsampler->downsample($ingestedTimeSeries->getDataPoints(), 1000),
                    "downSampled" => $this->LTTBDownsampler->downsample($ingestedTimeSeries->getDataPoints(), 100)
                ),
                "neighbours" => $this->neighbourhoodRenderer->renderNeighbourhood($neighbourhood),
                "hadPreprocessing" => $comparisonData["hadPreprocessing"],
	            "sfi" => $this->timeSeriesRenderer->renderSpecialFeatureIdentifiers($ingestedTimeSeries->getNormalizedFeatureVector()),
            );

            $this->response->setReturnBody(new JSONBody($response));
        }else{
            throw new EInvalidInputs("Unknown result key.");
        }
    }

}