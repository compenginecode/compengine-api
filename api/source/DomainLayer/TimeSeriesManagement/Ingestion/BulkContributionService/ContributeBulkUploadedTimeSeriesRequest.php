<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\BulkContributionService;

use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\Contributor\Contributor;
use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\ORM\Fingerprint\Fingerprint;
use DomainLayer\ORM\SamplingInformation\SamplingInformation;
use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\ORM\Source\Source;
use DomainLayer\ORM\TimeSeries\BulkUploadedTimeSeries\BulkUploadedTimeSeries;
use DomainLayer\TimeSeriesManagement\Ingestion\ContributionService\Requests\IContributeTimeSeriesRequest;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorGenerationService\FeatureVectorGenerationService;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\FeatureVectorNormalizationService;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\MappingManifestGenerationService\MappingManifestGenerationService;

/**
 * Class BulkContributeTimeSeriesRequest
 * @package DomainLayer\TimeSeriesManagement\Ingestion\BulkContributionService
 */
class ContributeBulkUploadedTimeSeriesRequest implements IContributeTimeSeriesRequest
{
    /** bulkUploadedTimeSeries
     *
     *
     *
     * @var BulkUploadedTimeSeries
     */
    private $timeSeries;

    /** featureVectorGenerationService
     *
     *
     *
     * @var FeatureVectorGenerationService
     */
    private $featureVectorGenerationService;

    /** mappingManifestGenerationService
     *
     *
     *
     * @var MappingManifestGenerationService
     */
    private $mappingManifestGenerationService;

    /** featureVectorNormalizationService
     *
     *
     *
     * @var FeatureVectorNormalizationService
     */
    private $featureVectorNormalizationService;

    /** siteAttributeRepository
     *
     *
     *
     * @var ISiteAttributeRepository
     */
    private $siteAttributeRepository;

    /** __construct
     *
     *  Constructor
     *
     * @param FeatureVectorGenerationService $featureVectorGenerationService
     * @param MappingManifestGenerationService $mappingManifestGenerationService
     * @param FeatureVectorNormalizationService $featureVectorNormalizationService
     * @param ISiteAttributeRepository $siteAttributeRepository
     */
    public function __construct(FeatureVectorGenerationService $featureVectorGenerationService, MappingManifestGenerationService $mappingManifestGenerationService, FeatureVectorNormalizationService $featureVectorNormalizationService, ISiteAttributeRepository $siteAttributeRepository) {
        $this->featureVectorGenerationService = $featureVectorGenerationService;
        $this->mappingManifestGenerationService = $mappingManifestGenerationService;
        $this->featureVectorNormalizationService = $featureVectorNormalizationService;
        $this->siteAttributeRepository = $siteAttributeRepository;
    }


    public function fill(BulkUploadedTimeSeries $bulkUploadedTimeSeries) {
        $this->timeSeries = $bulkUploadedTimeSeries;
    }

    /**	getName
     *
     * 	Returns the name of the time series.
     *
     * @return string
     */
    public function getName() {
        return $this->timeSeries->getName();
    }

    /**	getDescription
     *
     * 	Returns the description of the time series.
     *
     * @return string
     */
    public function getDescription() {
        return $this->timeSeries->getDescription();
    }

    /** getSource
     *
     * 	Returns the source associated to the time series. Returns NULL if none
     * 	is supplied.
     *
     * @return Source|NULL
     */
    public function getSource() {
        return $this->timeSeries->getSource();
    }

    /** getSamplingInformation
     *
     * 	Returns the sampling information associated to the time series.
     *
     * @return SamplingInformation
     */
    public function getSamplingInformation() {
        return $this->timeSeries->getSamplingInformation();
    }

    /** getCategory
     *
     * 	Returns the category associated to the time series.
     *
     * @return Category
     */
    public function getCategory() {
        return $this->timeSeries->getCategory();
    }

    /** getTags
     *
     * 	Returns all the tags associated with the time series.
     *
     * @return array
     */
    public function getTags() {
        return $this->timeSeries->getTags()->toArray();
    }

    /**	getDataPoints
     *
     * 	Returns an array of data points.
     *
     * @return array
     */
    public function getDataPoints() {
        return $this->timeSeries->getDataPoints();
    }

    /**	getRawFeatureVector
     *
     * 	Returns the raw feature vector.
     *
     * @return FeatureVector
     */
    public function getRawFeatureVector() {
        $manifest = $this->mappingManifestGenerationService->generateMappingManifestForCurrentFVFamily();
        return $this->featureVectorGenerationService->generateFeatureVector($this->getDataPoints(), $manifest);
    }

    /**	getNormalizedFeatureVector
     *
     * 	Returns the normalized feature vector.
     *
     * @return FeatureVector
     */
    public function getNormalizedFeatureVector() {
        $manifest = $this->mappingManifestGenerationService->generateMappingManifestForCurrentFVFamily();
        return $this->featureVectorNormalizationService->normalizeFeatureVector($this->getRawFeatureVector(), $manifest);
    }

    /**	getHashFamily
     *
     * 	Returns the array of hashes.
     *
     * @return Fingerprint
     */
    public function getHashFamily() {
        return new Fingerprint([], []);
    }

    /** getFeatureVectorFamily
     *
     * 	Returns the feature vector family to contribute the time series to.
     *
     * @return FeatureVectorFamily
     */
    public function getFeatureVectorFamily() {
        return $this->siteAttributeRepository->getCurrentFeatureVectorFamily();
    }

    /**	getContributor
     *
     * 	Returns the contributor, if present.
     *
     * @return Contributor|NULL
     */
    public function getContributor() {
        return $this->timeSeries->getContributor();
    }
}
