<?php

namespace DomainLayer\TimeSeriesManagement\TimeSeriesRenderer;

use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\Contributor\Contributor;
use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\ORM\SamplingInformation\SamplingInformation;
use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\ORM\Source\Source;
use DomainLayer\ORM\Tag\Tag;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\NearestNeighbourService;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\NeighbourhoodRenderer\NeighbourhoodRenderer;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery\SearchQuery;
use DomainLayer\TimeSeriesManagement\SpecialFeatureIdentification\Percentile\Percentile;
use DomainLayer\TimeSeriesManagement\SpecialFeatureIdentification\SpecialFeatureIdentificationService;
use InfrastructureLayer\ElasticSearch\ElasticSearch;

/**
 * Class TimeSeriesRenderer
 * @package DomainLayer\TimeSeriesManagement\TimeSeriesRenderer
 */
class TimeSeriesRenderer {

	private $elasticSearch;

	private $siteAttributeRepository;

	private $nearestNeighbourService;

	private $neighbourhoodRenderer;

	private $specialFeatureIdentificationService;

	public function __construct(ElasticSearch $elasticSearch, ISiteAttributeRepository $siteAttributeRepository,
		NearestNeighbourService $nearestNeighbourService, NeighbourhoodRenderer $neighbourhoodRenderer,
		SpecialFeatureIdentificationService $specialFeatureIdentificationService){

		$this->elasticSearch = $elasticSearch;
		$this->siteAttributeRepository = $siteAttributeRepository;
		$this->nearestNeighbourService = $nearestNeighbourService;
		$this->neighbourhoodRenderer = $neighbourhoodRenderer;
		$this->specialFeatureIdentificationService = $specialFeatureIdentificationService;
	}

	/** renderCategory
	 *
	 * 	Returns the rendered category.
	 *
	 * @param Category $category
	 * @return NULL|array
	 */
	protected function renderCategory(Category $category){
		$parentStr = '';
		$iterate = function(Category $category) use (&$parentStr, &$iterate){
			/** This is the slug pattern we use */
			$str = str_replace([' '], '-', strtolower($category->getName()));
			$str = str_replace("'", '', $str);

			$parentStr = $str . '/' . $parentStr;

			if ($category->hasParent()){
				$iterate($category->getParentCategory());
			}
		};

		$iterate($category);

		/** @var $source Source */
		return array(
			"name" => $category->getName(),
			"approvalStatus" => $category->getApprovalStatus()->chosenOption(),
			"uri" => $parentStr
		);
	}
	
	/** renderContributor
	 *
	 * 	Returns the rendered status.
	 *
	 * @param $contributor
	 * @return NULL|array
	 */
	protected function renderContributor($contributor){
		if (NULL === $contributor){
			return NULL;
		}

		/** @var $contributor Contributor */
		return array(
            "id" => $contributor->getId(),
            "name" => $contributor->getName()
		);
	}

	/** renderSource
	 *
	 * 	Returns the rendered status.
	 *
	 * @param $source
	 * @return NULL|array
	 */
	protected function renderSource($source){
		if (NULL === $source){
			return NULL;
		}

		/** @var $source Source */
		return array(
			"name" => $source->getName(),
			"approvalStatus" => $source->getApprovalStatus()->chosenOption()
		);
	}

	/** renderTags
	 *
	 * 	Returns the rendered array of tags.
	 *
	 * @param $tags
	 * @return NULL|array
	 */
	protected function renderTags($tags){
		$results = [];
		foreach($tags as $aTag){
			/** @var $aTag Tag */
			$results[] = array(
				"name" => $aTag->getName(),
                "slug" => $aTag->getSlug(),
				"approvalStatus" => $aTag->getApprovalStatus()->chosenOption()
			);
		};

		return $results;
	}

	public function renderSpecialFeatureIdentifiers(FeatureVector $normalizedFeatureVector){
		$sfiArray = $this->specialFeatureIdentificationService->findSpecialFeatures(
			$normalizedFeatureVector,
			$this->siteAttributeRepository->getCurrentFeatureVectorFamily()
		);

		$renderedResults = [];
		foreach($sfiArray as $anSFI){
			/** @var $anSFI Percentile */
			$renderedResults[] = array(
				"name" => $anSFI->getFeatureVectorDescriptor()->getName(),
				"prettyName" => $anSFI->getFeatureVectorDescriptor()->getPrettyName(),
				"value" => $anSFI->getPercentile()
			);
		}

		return $renderedResults;
	}

	/** renderSamplingInformation
	 *
	 * 	Returns the rendered SamplingInformation.
	 *
	 * @param SamplingInformation $samplingInformation
	 * @return NULL|array
	 */
	protected function renderSamplingInformation(SamplingInformation $samplingInformation){
		if ($samplingInformation->equals(SamplingInformation::SAMPLING_NOT_DEFINED) &&
		    !$samplingInformation->getSamplingRate() &&
			!$samplingInformation->getSamplingUnit()
		){
			return NULL;
		}

		return array(
			"samplingRate" => $samplingInformation->getSamplingRate(),
			"samplingUnit" => $samplingInformation->getSamplingUnit()
		);
	}

	protected function renderNearestNeighbours(PersistedTimeSeries $persistedTimeSeries, SearchQuery $searchQuery){
		return $this->neighbourhoodRenderer->renderNeighbourhood(
			$this->nearestNeighbourService->findNearestNeighbours(
				$persistedTimeSeries->getNormalizedFeatureVector(),
				$searchQuery
			)
		);
	}

	/** renderTimeSeriesForPublic
	 *
	 * 	Returns a rendered time series for the public API.
	 *
	 * @param PersistedTimeSeries $timeSeries
	 * @return array
	 */
	public function renderTimeSeriesForPublic(PersistedTimeSeries $timeSeries){
		return array(
			"id" => $timeSeries->getId(),
			"name" => $timeSeries->getName(),
			"description" => $timeSeries->getDescription(),
			"source" => $this->renderSource($timeSeries->getSource()),
			"tags" => $this->renderTags($timeSeries->getTags()),
			"samplingInformation" => $this->renderSamplingInformation($timeSeries->getSamplingInformation()),
			"category" => $this->renderCategory($timeSeries->getCategory()),
			"sfi" => $this->renderSpecialFeatureIdentifiers($timeSeries->getNormalizedFeatureVector()),
			"timeSeries" => array(
				"raw" => $timeSeries->getDataPoints(),
			)
		);
	}

	/** renderTimeSeries
	 *
	 * 	Returns a rendered time series.
	 *
	 * @param PersistedTimeSeries $timeSeries
	 * @param SearchQuery $searchQuery
	 * @return array
	 */
	public function renderTimeSeries(PersistedTimeSeries $timeSeries, SearchQuery $searchQuery){
		return array(
			"id" => $timeSeries->getId(),
			"eid" => $timeSeries->getDocumentId(),
			"name" => $timeSeries->getName(),
			"description" => $timeSeries->getDescription(),
			"source" => $this->renderSource($timeSeries->getSource()),
			"tags" => $this->renderTags($timeSeries->getTags()),
			"samplingInformation" => $this->renderSamplingInformation($timeSeries->getSamplingInformation()),
			"contributor" => $this->renderContributor($timeSeries->getContributor()),
			"category" => $this->renderCategory($timeSeries->getCategory()),
			"sfi" => $this->renderSpecialFeatureIdentifiers($timeSeries->getNormalizedFeatureVector()),
			"neighbours" => $this->renderNearestNeighbours($timeSeries, $searchQuery),
			"timeSeries" => array(
				"downSampled" => $timeSeries->getDownSampledDataPoints30(),
				"raw" => $timeSeries->getDataPoints(),
			),
		);
	}

	public function renderTimeSeriesBriefly(PersistedTimeSeries $timeSeries){
		return array(
			"id" => $timeSeries->getId(),
			"eid" => $timeSeries->getDocumentId(),
			"name" => $timeSeries->getName(),
			"description" => $timeSeries->getDescription(),
			"source" => $this->renderSource($timeSeries->getSource()),
			"tags" => $this->renderTags($timeSeries->getTags()),
			"samplingInformation" => $this->renderSamplingInformation($timeSeries->getSamplingInformation()),
			"sfi" => $this->renderSpecialFeatureIdentifiers($timeSeries->getNormalizedFeatureVector()),
			"contributor" => $this->renderContributor($timeSeries->getContributor()),
			"category" => $this->renderCategory($timeSeries->getCategory()),
			"timeSeries" => array(
				"raw" => $timeSeries->getDataPoints(),
			)
		);
	}

    public function renderSimple(PersistedTimeSeries $timeSeries) {
        return [
            "id" => $timeSeries->getId(),
			"name" => $timeSeries->getName(),
			"description" => $timeSeries->getDescription(),
            "contributor" => $this->renderContributor($timeSeries->getContributor()),
			"source" => $this->renderSource($timeSeries->getSource()),
			"tags" => $this->renderTags($timeSeries->getTags()),
			"category" => $this->renderCategory($timeSeries->getCategory()),
            "timeSeries" => array(
                "downSampled" => $timeSeries->getDownSampledDataPoints30(),
                "raw" => $timeSeries->getDataPoints(),
            ),
        ];
	}

}