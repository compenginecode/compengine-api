<?php

namespace DomainLayer\TimeSeriesManagement\Comparison\ComparisonService;

use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\ORM\TimeSeries\IngestedTimeSeries\IngestedTimeSeries;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonService\Exceptions\EEmptyTimeSeries;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonStatus\ComparisonStatus;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonStatusService\ComparisonStatusService;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonStore\ComparisonStore;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\NearestNeighbourService;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery\SearchQuery;
use DomainLayer\TimeSeriesManagement\Comparison\UploadService\UploadService;
use DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester\TimeSeriesIngester;

/**
 * Class ComparisonService
 * @package DomainLayer\TimeSeriesManagement\Comparison\ComparisonService
 */
class ComparisonService {

	/** $comparisonStatusService
	 *
	 * 	Service used for changing the status
	 * 	of comparison jobs.
	 *
	 * @var ComparisonStatusService
	 */
	private $comparisonStatusService;

	/** $timeSeriesIngester
	 *
	 * 	Service used to ingest files as time series.
	 *
	 * @var TimeSeriesIngester
	 */
	private $timeSeriesIngester;

	/** $uploadService
	 *
	 * 	Service used for managing the actual file that was uploaded
	 * 	by the user.
	 *
	 * @var UploadService
	 */
	private $uploadService;

	/** $comparisonStore
	 *
	 * 	Interface to storing and retrieving comparison results.
	 *
	 * @var ComparisonStore
	 */
	private $comparisonStore;

	/**
	 * @var NearestNeighbourService
	 */
	private $nearestNeighbourService;

	/**
	 * @var ISiteAttributeRepository
	 */
	private $siteAttributeRepository;

	/** $hadPreprocessing
	 *
	 * 	Is set to TRUE if preprocessing occurred and FALSE otherwise.
	 *
	 * @var bool
	 */
	private $hadPreprocessing = FALSE;

	/** setupEvents
	 *
	 * 	Sets up all the events listeners on the $timeSeriesIngester instance. For each event
	 * 	we change the status of the particular comparison job.
	 *
	 * @param $comparisonKey
	 */
	private function setupEvents($comparisonKey){
		$self = $this;

		$this->timeSeriesIngester->on(TimeSeriesIngester::EVENT_CONVERSION_STARTED,
			function($conversionType) use ($self, $comparisonKey){
				$self->comparisonStatusService->updateStatus($comparisonKey, ComparisonStatus::conversionStarted($conversionType));
			}
		);

		$this->timeSeriesIngester->on(TimeSeriesIngester::EVENT_PRE_PROCESSING_STARTED,
			function() use ($self, $comparisonKey){
				$self->comparisonStatusService->updateStatus($comparisonKey, ComparisonStatus::preprocessingStarted());
				$self->hadPreprocessing = TRUE;
			}
		);

		$this->timeSeriesIngester->on(TimeSeriesIngester::EVENT_PROCESS_FINISHED,
			function() use ($self, $comparisonKey){
				$self->comparisonStatusService->updateStatus($comparisonKey, ComparisonStatus::processFinished());
			}
		);
	}

	/** __construct
	 *
	 * 	ComparisonService constructor.
	 *
	 * @param ComparisonStatusService $comparisonStatusService
	 * @param TimeSeriesIngester $timeSeriesIngester
	 * @param UploadService $uploadService
	 * @param ComparisonStore $comparisonStore
	 * @param NearestNeighbourService $nearestNeighbourService
	 */
	public function __construct(ComparisonStatusService $comparisonStatusService, TimeSeriesIngester $timeSeriesIngester,
		UploadService $uploadService, ComparisonStore $comparisonStore,
		NearestNeighbourService $nearestNeighbourService, ISiteAttributeRepository $siteAttributeRepository){

		$this->comparisonStatusService = $comparisonStatusService;
		$this->timeSeriesIngester = $timeSeriesIngester;
		$this->uploadService = $uploadService;
		$this->comparisonStore = $comparisonStore;
		$this->nearestNeighbourService = $nearestNeighbourService;
		$this->siteAttributeRepository = $siteAttributeRepository;
	}

	/** startComparison
	 *
	 * 	Starts a comparison process. A valid comparison key must be supplied. Returns the time
	 * 	series data
	 *
	 * @param IComparisonRequest $comparisonRequest
	 * @return string
	 * @throws \DomainLayer\TimeSeriesManagement\Comparison\UploadService\Exceptions\EComparisonKeyMissing
	 * @throws EEmptyTimeSeries
	 */
	public function startComparison(IComparisonRequest $comparisonRequest){
        $comparisonKey = $comparisonRequest->getComparisonKey();
		$this->setupEvents($comparisonKey);
		$fileData = $this->uploadService->getFileData($comparisonKey);
		$localPath = $fileData["temporaryFilePath"];

		/** @var IngestedTimeSeries $ingestedTimeSeries */
		$ingestedTimeSeries = $this->timeSeriesIngester->ingestFile($localPath, $comparisonRequest->shouldIgnoreTruncationWarning());

		$searchQuery = new SearchQuery($this->siteAttributeRepository->getCurrentFeatureVectorFamily()->getCommonIndex());
		$neighbours = $this->nearestNeighbourService->findNearestNeighbours(
			$ingestedTimeSeries->getNormalizedFeatureVector(), $searchQuery);

		$results = array(
			"timeSeries" => $ingestedTimeSeries,
			"neighbours" => $neighbours,
			"hadPreprocessing" => $this->hadPreprocessing
		);

		/** Remove the local file */
		unlink($localPath);

		$resultKey = $this->comparisonStore->temporarilyStoreComparisonResult($results);

		return $resultKey;
	}

}