<?php

namespace DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService;

use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository\ITimeSeriesRepository;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\Neighbourhood\Neighbourhood;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery\CategoryFilter\CategoryFilter;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery\SearchQuery;
use InfrastructureLayer\Caching\CacheAdaptor\ICacheAdaptor;
use InfrastructureLayer\ElasticSearch\ElasticSearch;

/**
 * Class NearestNeighbourService
 * @package DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService
 */
class NearestNeighbourService {

	private $elasticSearch;

	private $siteAttributeRepository;

	private $timeSeriesRepository;

	private $cacheAdaptor;

	protected function getFingerprint(FeatureVector $featureVector, CategoryFilter $categoryFilter){
		$currentFVF = $this->siteAttributeRepository->getCurrentFeatureVectorFamily();
		if ($categoryFilter->equals(CategoryFilter::FILTER_SYNTHETIC)){
			$index = $currentFVF->getSyntheticIndex();
		}else if ($categoryFilter->equals(CategoryFilter::FILTER_REAL)){
			$index = $currentFVF->getRealIndex();
		}else{
			$index = $currentFVF->getCommonIndex();
		}

		return $index->hashTables()->generateHash($featureVector->toVector());
	}

	protected function getNeighbouringTimeSeries(FeatureVector $normalizedCandidateFeatureVector, SearchQuery $searchQuery){
		$fingerprint = $this->getFingerprint($normalizedCandidateFeatureVector, $searchQuery->getCategoryFilter());
		$neighbours = $this->elasticSearch->findNearestNeighbours(
			$fingerprint,
			$searchQuery,
			$this->siteAttributeRepository->getCurrentFeatureVectorFamily()->getIndexName()
		);

		$matches = [];
		foreach($neighbours["hits"]["hits"] as $aNeighbour){
			$timeSeriesId = $aNeighbour["_source"]["timeSeriesId"];
			$normalizedFeatureVectorArray = $aNeighbour["_source"]["normalizedFeatureVector"];
			$normalizedFeatureVector = new FeatureVector($normalizedFeatureVectorArray);

			$distance = $normalizedFeatureVector->toVector()->distance($normalizedCandidateFeatureVector->toVector());
			$matches[$timeSeriesId] = $distance;
		}

		uasort($matches, function($a, $b){
			return $a > $b;
		});

		return $matches;
	}

	private function cacheResults(array $neighbours, FeatureVector $featureVector, SearchQuery $searchQuery){
		$hashKey = md5($featureVector->toHash() . $searchQuery->toHash());
		$cacheKey = __CLASS__ . $hashKey;
		$this->cacheAdaptor->setValue($cacheKey, serialize($neighbours), 300);
	}

	private function getFromCache(FeatureVector $featureVector, SearchQuery $searchQuery){
		$hashKey = md5($featureVector->toHash() . $searchQuery->toHash());
		$cacheKey = __CLASS__ . $hashKey;
		$result = $this->cacheAdaptor->getValue($cacheKey);
		return (NULL === $result) ? NULL : unserialize($result);
	}

	public function __construct(ElasticSearch $elasticSearch, ISiteAttributeRepository $siteAttributeRepository,
		ITimeSeriesRepository $timeSeriesRepository, ICacheAdaptor $cacheAdaptor){

		$this->elasticSearch = $elasticSearch;
		$this->siteAttributeRepository = $siteAttributeRepository;
		$this->timeSeriesRepository = $timeSeriesRepository;
		$this->cacheAdaptor = $cacheAdaptor;
	}

	/**
	 * @param FeatureVector $normalizedCandidateFeatureVector
	 * @param SearchQuery $searchQuery
	 *
	 * @return Neighbourhood
	 */
	public function findNearestNeighbours(FeatureVector $normalizedCandidateFeatureVector, SearchQuery $searchQuery){
		$totalNumberOfSecondaryNodes = 3;

		$neighbours = $this->getNeighbouringTimeSeries($normalizedCandidateFeatureVector, $searchQuery);

		$neighbourhood = new Neighbourhood();

		$neighbours = array_slice($neighbours, 0, $searchQuery->getDirectNeighbourLimit());
		foreach($neighbours as $aNeighbourId => $similarityScore){
			$neighbourhood->addNodeToRoot($aNeighbourId, $similarityScore);
		}

		/** We need to make sure the graph is unidirected. To do this,
		 * 	we just keep track of each edge and check if it exists before we
		 *  re-add it. **/
		$hitCache = [];


		$nodes = $neighbourhood->getNodes();
		foreach($nodes as $aNode){
			$aNeighbourId = $aNode["id"];
			$timeSeriesObj = $this->timeSeriesRepository->findById($aNeighbourId);
			if (NULL !== $timeSeriesObj && $totalNumberOfSecondaryNodes > 0){
				$localNeighbours = $this->getNeighbouringTimeSeries(
					$timeSeriesObj->getNormalizedFeatureVector(), $searchQuery);

				$localNeighbours = array_slice($localNeighbours, 0, $totalNumberOfSecondaryNodes);

				foreach($localNeighbours as $aLocalNeighbourId => $localSimilarityScore){
					$needle = $aNeighbourId . $aLocalNeighbourId;

					if ($aLocalNeighbourId !== $aNeighbourId && !in_array($needle, $hitCache)) {
						$neighbourhood->addNodeToNode($aNeighbourId, $aLocalNeighbourId, $localSimilarityScore);

						/** We add both directions */
						$hitCache[] = $aNeighbourId . $aLocalNeighbourId;
						$hitCache[] = $aLocalNeighbourId . $aNeighbourId;
					}
				}
			}
		}

		return $neighbourhood;
	}

}