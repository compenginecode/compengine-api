<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\ContributionService;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Fingerprint\Fingerprint;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\ORM\TopLevelCategory\TopLevelCategory;
use DomainLayer\SimilarUploadNotifierService\SimilarUploadNotifierService;
use DomainLayer\TimeSeriesManagement\Ingestion\ContributionService\Requests\IContributeTimeSeriesRequest;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorHashService\FeatureVectorHashService;

/**
 * Class ContributionService
 * @package DomainLayer\TimeSeriesManagement\Ingestion\ContributionService
 */
class ContributionService {

    /**
     * @var EntityManager
     */
	private $entityManager;

    /**
     * @var FeatureVectorHashService
     */
	private $featureVectorHashService;

    /**
     * @var SimilarUploadNotifierService
     */
	private $similarUploadNotifierService;

    /**
     * ContributionService constructor.
     * @param EntityManager $entityManager
     * @param FeatureVectorHashService $featureVectorHashService
     * @param SimilarUploadNotifierService $similarUploadNotifierService
     */
    public function __construct(
        EntityManager $entityManager,
        FeatureVectorHashService $featureVectorHashService,
        SimilarUploadNotifierService $similarUploadNotifierService
    ) {
        $this->entityManager = $entityManager;
        $this->featureVectorHashService = $featureVectorHashService;
        $this->similarUploadNotifierService = $similarUploadNotifierService;
    }

    /** contributeTimeSeries
	 *
	 * 	Contributes permanently the given time series.
	 *
	 * @param IContributeTimeSeriesRequest $contributeTimeSeriesRequest
	 * @return PersistedTimeSeries
	 * @throws \Exception
	 */
	public function contributeAndFlushTimeSeries(IContributeTimeSeriesRequest $contributeTimeSeriesRequest, $origin){
		/** Now create a persisted time series */
		$timeSeries = new PersistedTimeSeries(
			$contributeTimeSeriesRequest->getDataPoints(),
			$contributeTimeSeriesRequest->getRawFeatureVector(),
			$contributeTimeSeriesRequest->getNormalizedFeatureVector(),
			new Fingerprint([], []), //recalculated?
			$contributeTimeSeriesRequest->getName(),
			$contributeTimeSeriesRequest->getDescription(),
			$contributeTimeSeriesRequest->getSource(),
			$contributeTimeSeriesRequest->getCategory(),
			$contributeTimeSeriesRequest->getSamplingInformation(),
			$contributeTimeSeriesRequest->getTags()
		);

		/** Set the contributor */
		$timeSeries->setContributor($contributeTimeSeriesRequest->getContributor());

        /** Set the hash (for identifying duplicates) */
        $hash = hash("sha256", implode(",", $timeSeries->getDataPoints()));
        $timeSeries->setHash($hash);
        $timeSeries->setOrigin($origin);

		/** Flush and save into the database */
		$this->entityManager->persist($timeSeries);
		$this->entityManager->flush();
		$this->entityManager->refresh($timeSeries);

        $this->similarUploadNotifierService->notifySimilarUploads($timeSeries);

		return $timeSeries;
	}

}