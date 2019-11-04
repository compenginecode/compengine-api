<?php

namespace PresentationLayer\Routes\Boot\Get;

use ConfigurationLayer\ApplicationConfig\ApplicationConfig;
use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository\DatabaseTimeSeriesRepository;
use DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService\TimeSeriesDownloadService;
use InfrastructureLayer\HumanReadableNumbers\HumanReadableNumbers;
use PresentationLayer\ReturnBody\TextReturnBody;
use Yam\Route\AbstractRoute;

/**
 * Class Get
 * @package PresentationLayer\Routes\Store\Post
 */
class Get extends AbstractRoute{

    private $siteAttributeRepository;

	private $timeSeriesDownloadService;

	private $applicationConfig;

	private $timeSeriesRepository;

    public function __construct(ISiteAttributeRepository $siteAttributeRepository,
	    TimeSeriesDownloadService $timeSeriesDownloadService,
        ApplicationConfig $applicationConfig,
        DatabaseTimeSeriesRepository $timeSeriesRepository){
        $this->siteAttributeRepository = $siteAttributeRepository;
	    $this->timeSeriesDownloadService = $timeSeriesDownloadService;
	    $this->applicationConfig = $applicationConfig;
        $this->timeSeriesRepository = $timeSeriesRepository;
    }

    /** execute
     *
     *  Route execution.
     *
     */
    public function execute(){
    	try {
		    $csvSize = filesize($this->timeSeriesDownloadService->getLatestTimeSeries("csv"));
	    } catch (\Exception $e) {
		    $csvSize = NULL;
	    }
	    try {
		    $jsonSize = filesize($this->timeSeriesDownloadService->getLatestTimeSeries("json"));
	    } catch (\Exception $e) {
		    $jsonSize = NULL;
	    }

	    $timeSeriesCount = $this->timeSeriesRepository->createQueryBuilder('ts')->select('COUNT(ts.id)')->getQuery()->getSingleScalarResult();

    	$humanReadableTotalDataPoints = HumanReadableNumbers::numberToHumanReadableString($this->siteAttributeRepository->getTotalDataPoints());
    	$humanReadableTimeSeriesCount = HumanReadableNumbers::numberToHumanReadableString($timeSeriesCount);

        $mockResponseObj = array(
            "statisticsMessages" => array(
                "$humanReadableTotalDataPoints data points",
                "$humanReadableTimeSeriesCount time series"
            ),
            "version" => "0.10.0",
            "settings" => array(
                "comparisonResultTimeout" => $this->siteAttributeRepository->getComparisonResultCacheTime()*1000,
                "hardFileSizeLimit" => 500,
                "maxTotalBulkUploadSize" => (int) $this->applicationConfig->get("max_total_bulk_upload_size"),
                "supportedFileExtensions" => array(
                    ".csv", ".xlsx", ".xls", ".txt", ".dat", ".wav", ".mp3"
                )
            ),
	        "timeSeriesCount" => $timeSeriesCount,
	        "exportedDataSizes" => [
	        	"json" => $jsonSize,
		        "csv" => $csvSize
	        ]
        );

        $response = "var GLOBAL = " . json_encode($mockResponseObj) . ";";

        $this->response->setReturnBody(new TextReturnBody($response));
    }

}