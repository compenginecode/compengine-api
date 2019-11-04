<?php

namespace DomainLayer\SimilarUploadNotifierService;

use DomainLayer\ContributorService\ContributorService;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository\ITimeSeriesRepository;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\NearestNeighbourService;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\Neighbourhood\Neighbourhood;
use PresentationLayer\Routes\Timeseries\SearchQueryFactory;

class SimilarUploadNotifierService
{
    /**
     * @var NearestNeighbourService
     */
    private $nearestNeighbourService;

    /**
     * @var ITimeSeriesRepository
     */
    private $timeSeriesRepository;

    /**
     * @var SearchQueryFactory
     */
    private $searchQueryFactory;

    /**
     * @var ContributorService
     */
    private $contributorService;

    /**
     * SimilarUploadNotifierService constructor.
     * @param NearestNeighbourService $nearestNeighbourService
     * @param ITimeSeriesRepository $timeSeriesRepository
     * @param SearchQueryFactory $searchQueryFactory
     * @param ContributorService $contributorService
     */
    public function __construct(
        NearestNeighbourService $nearestNeighbourService,
        ITimeSeriesRepository $timeSeriesRepository,
        SearchQueryFactory $searchQueryFactory,
        ContributorService $contributorService
    ) {
        $this->nearestNeighbourService = $nearestNeighbourService;
        $this->timeSeriesRepository = $timeSeriesRepository;
        $this->searchQueryFactory = $searchQueryFactory;
        $this->contributorService = $contributorService;
    }

    /**
     * Triggers a notification for the 5 most similar time series contributors.
     *
     * @param PersistedTimeSeries $persistedTimeSeries
     */
    public function notifySimilarUploads(PersistedTimeSeries $persistedTimeSeries)
    {
        $neighbourhood = $this->nearestNeighbourService->findNearestNeighbours(
            $persistedTimeSeries->getNormalizedFeatureVector(),
            $this->searchQueryFactory->constructSearchQuery([], 5)
        );

        $timeSeries = $this->getTimeSeriesFromNeighbourhood($neighbourhood);

        array_walk($timeSeries, function (PersistedTimeSeries $timeSeries) use ($persistedTimeSeries) {
            if ($contributor = $timeSeries->getContributor()) {
                $this->contributorService->newSimilarUploadNotification($contributor, $persistedTimeSeries);
            }
        });
    }

    protected function getTimeSeriesFromNeighbourhood(Neighbourhood $neighbourhood){
        /** We want to get an array of all the time series IDs */
        $ids = array_map(function($element){
            return $element["id"];
        }, $neighbourhood->getNodes());

		/** We remove the root */
		$ids = array_filter($ids, function($element){
			return "root" !== $element;
		});

        if (0 === count($ids)){
            return [];
        }

        /** We then retrieve all the time series objects from Doctrine/MySQL in one call
         * 	and define a lookup function to be used locally. */
        return $this->timeSeriesRepository->findManyByIds(...$ids);
    }
}
