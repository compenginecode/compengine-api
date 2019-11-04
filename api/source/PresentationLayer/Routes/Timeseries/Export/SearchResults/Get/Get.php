<?php

namespace PresentationLayer\Routes\Timeseries\Export\SearchResults\Get;

use ConfigurationLayer\ApplicationConfig\ApplicationConfig;
use DomainLayer\TimeSeriesManagement\SearchResultsDownloadService\SearchResultsDownloadService;
use DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService\TimeSeriesDownloadService;
use InfrastructureLayer\Caching\CacheAdaptor\ICacheAdaptor;
use PresentationLayer\Routes\EBadRequest;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\Timeseries\SearchQueryFactory;
use PresentationLayer\Routing\StatusCode\UnprocessableEntity;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;
use Yam\Route\Response\StatusCode\StatusBadRequest;

/**
 * Class Get
 * @package PresentationLayer\Routes\Timeseries\Export\SearchResults\Get
 */
class Get extends AbstractRoute {

	private $cacheAdaptor;

	private $searchResultsDownloadService;

	public function __construct(ICacheAdaptor $cacheAdaptor, SearchResultsDownloadService $searchResultsDownloadService){
		$this->cacheAdaptor = $cacheAdaptor;
		$this->searchResultsDownloadService = $searchResultsDownloadService;
	}

	public function execute() {
		$data = $this->cacheAdaptor->getValue($_GET["token"]);
		if (NULL === $data){
			throw new EInvalidInputs("Invalid token.");
		}

		$data = unserialize($data);

		if (is_array($data["ids"])){
			$ids = $data["ids"];
		}else{
			$ids = [$data["ids"]];
		}

		if ("json" === $data["type"]){
			$this->searchResultsDownloadService->exportToJSON($ids);
		}else{
			$this->searchResultsDownloadService->exportToCSV($ids);
		}
	}

}