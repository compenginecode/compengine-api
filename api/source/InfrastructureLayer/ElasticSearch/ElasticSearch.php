<?php

namespace InfrastructureLayer\ElasticSearch;

use ConfigurationLayer\ApplicationConfig\ApplicationConfig;
use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\FeatureVector\Exceptions\EFeatureElementMappingIdNotPresent;
use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\ORM\FeatureVectorDocument\FeatureVectorDocument;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\ORM\FeatureVectorIndex\FeatureVectorIndex;
use DomainLayer\ORM\Fingerprint\Fingerprint;
use DomainLayer\ORM\HashTable\HashTable;
use DomainLayer\ORM\Source\Source;
use DomainLayer\ORM\TopLevelCategory\TopLevelCategory;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery\CategoryFilter\CategoryFilter;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery\SearchQuery;
use Elasticsearch\Client;
use InfrastructureLayer\ElasticSearch\Exceptions\EFeatureVectorDocumentMismatch;
use InfrastructureLayer\ElasticSearch\QueryLog\IQueryLog;
use SendGrid\Email;

/**
 * Class ElasticSearch
 * @package InfrastructureLayer\ElasticSearch
 */
class ElasticSearch {

	const DOCUMENT_TYPE_FVD = "fvd";

	/** $elasticSearchClient
	 *
	 * 	Interface to the ElasticSearch client, which we can make real
	 * 	requests with to the ElasticSearch cluster.
	 *
	 * @var Client
	 */
	private $elasticSearchClient;

	private $queryLog;

	private $sendGrid;

	private $applicationConfig;

	protected function getSFIPercentileQuery($indexKey, array $featureVectorElementIds, FeatureVector $normalizedFeatureVector){
		$queryDSL = array(
			"size" => 0,
			"body" => array(
				"query" => array(
					"match" => array(
						"_index" => $indexKey
					)
				)
			)
		);

		$localAggregation = [];
		foreach($featureVectorElementIds as $aFieldName){
			$localAggregation[$aFieldName] = array(
				"percentile_ranks" => array(
					"field" => "normalizedFeatureVector.$aFieldName",
					"values" => [$normalizedFeatureVector->getElementValue($aFieldName)]
				)
			);

		}

		$queryDSL["body"]["aggregations"] = $localAggregation;

		return $queryDSL;
	}

	/** getRawFeatureVectorPercentilesQuery
	 *
	 * 	Returns a valid ElasticSearch query to get the percentiles for the raw feature
	 * 	vectors in the given feature vector family.
	 *
	 * @param $indexKey
	 * @param array $featureVectorElementIds
	 * @return array
	 */
	protected function getRawFeatureVectorPercentilesQuery($indexKey, array $featureVectorElementIds){
		$percents = [25, 50, 75];

		$queryDSL = array(
			"size" => 0,
			"body" => array(
				"query" => array(
					"bool" => array(
						"must" => array(
							"match" => array(
								"_index" => $indexKey
							)
						)
					)
				),
				"aggs" => array()
			)
		);

		foreach($featureVectorElementIds as $aFieldName){
			$localAggregation = array(
				$aFieldName => array(
					"percentiles" => array(
						"percents" => $percents,
						"field" => "rawFeatureVector.$aFieldName"
					)
				)
			);

			$queryDSL["body"]["aggs"] = array_merge($queryDSL["body"]["aggs"], $localAggregation);
		}

		return $queryDSL;
	}

	/** getRawFeatureVectorPercentilesQuery
	 *
	 * 	Returns a valid ElasticSearch query to get the percentiles for the raw feature
	 * 	vectors in the given feature vector family.
	 *
	 * @param $indexKey
	 * @param array $featureVectorElementIds
	 * @return array
	 */
	protected function getRawFeatureVectorHistogramQuery($indexKey, array $featureVectorElementIds){
		$queryDSL = array(
			"size" => 0,
			"body" => array(
				"query" => array(
					"bool" => array(
						"must" => array(
							"match" => array(
								"_index" => $indexKey
							)
						)
					)
				),
				"aggs" => array()
			)
		);

		foreach($featureVectorElementIds as $aFieldName){
			$localAggregation = array(
				$aFieldName => array(
					"histogram" => array(
						"interval" => 50000,
						"field" => "rawFeatureVector.$aFieldName"
					)
				)
			);

			$queryDSL["body"]["aggs"] = array_merge($queryDSL["body"]["aggs"], $localAggregation);
		}

		return $queryDSL;
	}

	protected function sendLowNodeAlert(array $query){
		$text = json_encode($query, JSON_PRETTY_PRINT) . PHP_EOL;
		file_put_contents(ROOT_PATH . "/private/logs/low-node-alert.log", $text, FILE_APPEND);
	}

	/** __construct
	 *
	 * 	ElasticSearch constructor.
	 *
	 * @param Client $elasticSearchClient
	 */
	public function __construct(Client $elasticSearchClient, IQueryLog $queryLog,
		ApplicationConfig $applicationConfig){

		$this->elasticSearchClient = $elasticSearchClient;
		$this->queryLog = $queryLog;
		$this->applicationConfig = $applicationConfig;
	}

	public function getSpecialFeatureIdentifiers($indexKey, array $featureVectorElementIds, FeatureVector $normalizedFeatureVector){
		$queryDSL = $this->getSFIPercentileQuery($indexKey, $featureVectorElementIds, $normalizedFeatureVector);

		$this->queryLog->log($queryDSL);
		$results = $this->elasticSearchClient->search($queryDSL);

		$response = [];
		foreach($results["aggregations"] as $aggregationKey => $valueArray){
			$response[$aggregationKey] = array_values($valueArray["values"])[0];
		}

		return $response;
	}

	/** getRawFeatureVectorPercentiles
	 *
	 * 	Returns a percentile read out in the form of an array, for all the feature vector element mapping
	 * 	IDs supplied within the given index.
	 *
	 * @param $indexKey
	 * @param array $featureVectorElementMappingIds
	 * @return array
	 */
	public function getRawFeatureVectorPercentiles($indexKey, array $featureVectorElementMappingIds){
		$query = $this->getRawFeatureVectorPercentilesQuery($indexKey, $featureVectorElementMappingIds);
		$results = $this->elasticSearchClient->search($query);

		$cleanedResults = [];
		foreach($results["aggregations"] as $aFieldName => $percentiles){
			$cleanedResults[$aFieldName] = $percentiles["values"];
		}

		return $cleanedResults;
	}

	/** getRawFeatureVectorHistogram
	 *
	 * 	Returns a histogram read out in the form of an array, for all the feature vector element mapping
	 * 	IDs supplied within the given index.
	 *
	 * @param $indexKey
	 * @param array $featureVectorElementMappingIds
	 * @return array
	 */
	public function getRawFeatureVectorHistogram($indexKey, array $featureVectorElementMappingIds){
		$query = $this->getRawFeatureVectorHistogramQuery($indexKey, $featureVectorElementMappingIds);
		$results = $this->elasticSearchClient->search($query);

		$cleanedResults = [];
		foreach($results["aggregations"] as $aFieldName => $buckets){
			$cleanedResults[$aFieldName] = array();
			foreach($buckets["buckets"] as $aBucketContents){
				$cleanedResults[$aFieldName][] = array(
					"partition" => $aBucketContents["key"],
					"count" => $aBucketContents["doc_count"]
				);
			}
		}

		return $cleanedResults;
	}

	private function getFingerprintFieldTypes(FeatureVectorIndex $featureVectorIndex){
		$fingerprintRestrictions = [];
		foreach($featureVectorIndex->hashTables() as $aHashTable){
			/** @var $aHashTable HashTable */
			$fingerprintRestrictions[$aHashTable->getId()] = array(
				"type" => "string",
				"index" => "not_analyzed"
			);
		};

		return $fingerprintRestrictions;
	}

	public function createIndexForFeatureVectorFamily(FeatureVectorFamily $featureVectorFamily){
		$commonIndexFieldTypes = $this->getFingerprintFieldTypes($featureVectorFamily->getCommonIndex());
		$syntheticIndexFieldTypes = $this->getFingerprintFieldTypes($featureVectorFamily->getSyntheticIndex());
		$realIndexFieldTypes = $this->getFingerprintFieldTypes($featureVectorFamily->getRealIndex());

		$categoryIndexFieldTypes = array_merge($syntheticIndexFieldTypes, $realIndexFieldTypes);

		$this->elasticSearchClient->indices()->create(array(
			"index" => $featureVectorFamily->getIndexName(),
			"body" => array(
				"mappings" => array(
					self::DOCUMENT_TYPE_FVD => array(
						"properties" => array(
                            "description" => array(
                                "type" => "string"
                            ),
                            "name" => array(
                                "type" => "string"
                            ),
							"source" => array(
								"type" => "string",
							),
                            "sourceKeyword" => array(
                                "type" => "string",
                                "index" => "not_analyzed"
                            ),
                            "category" => array(
                                "type" => "string",
                            ),
                            "categoryKeyword" => array(
                                "type" => "string",
                                "index" => "not_analyzed"
                            ),
							"topLevelCategory" => array(
								"type" => "string",
								"index" => "not_analyzed"
							),
							"timeSeriesId" => array(
								"type" => "string",
								"index" => "not_analyzed"
							),
							"commonFingerprint" => array(
								"type" => "nested",
								"properties" => $commonIndexFieldTypes
							),
							"categoryFingerprint" => array(
								"type" => "nested",
								"properties" => $categoryIndexFieldTypes
							)
						)
					)
				)
			)
		));
	}

	public function saveFeatureVectorDocument(FeatureVector $rawFeatureVector, FeatureVector $normalizedFeatureVector,
		Fingerprint $fingerprint, Category $topLevelCategory, Category $category, array $tags, $description, $name, $source, $timeSeriesId, $indexName){

		if ($source !== NULL && !$source instanceof Source){
			throw new \Exception("Source must be NULL or an instance of Source.");
		}

		$response = $this->elasticSearchClient->index(array(
			"index" => $indexName,
			"type" => self::DOCUMENT_TYPE_FVD,
			"body" => array(
			    "description" => $description,
                "name" => $name,
                "source" => NULL === $source ? NULL : $source->getName(),
                "sourceKeyword" => NULL === $source ? NULL : $source->getName(),
				"tags" => $tags,
                "category" => strtolower($category->getName()),
                "categoryKeyword" => strtolower($category->getName()),
				"topLevelCategory" => strtolower($topLevelCategory->getName()),
				"rawFeatureVector" => $rawFeatureVector->toArray(),
				"normalizedFeatureVector" => $normalizedFeatureVector->toArray(),
				"commonFingerprint" => $fingerprint->getCommonFingerprint(),
				"categoryFingerprint" => $fingerprint->getCategoryFingerprint(),
				"timeSeriesId" => $timeSeriesId
			)
		));
		$this->elasticSearchClient->indices()->refresh();

		return $response["_id"];
	}

	public function updateFeatureVectorDocument(FeatureVector $rawFeatureVector, FeatureVector $normalizedFeatureVector,
		Fingerprint $fingerprint, Category $topLevelCategory, Category $category, array $tags, $description, $name, $source, $elasticSearchDocumentId, $indexName){

		if ($source !== NULL && !$source instanceof Source){
			throw new \Exception("Source must be NULL or an instance of Source.");
		}

		$query = array(
			"id" => $elasticSearchDocumentId,
			"index" => $indexName,
			"type" => self::DOCUMENT_TYPE_FVD,
			"body" => array(
				"doc" => array(
                    "description" => $description,
                    "name" => $name,
					"source" => NULL === $source ? NULL : $source->getName(),
					"tags" => $tags,
					"topLevelCategory" => strtolower($topLevelCategory->getName()),
                    "category" => strtolower($category->getName()),
					"rawFeatureVector" => $rawFeatureVector->toArray(),
					"normalizedFeatureVector" => $normalizedFeatureVector->toArray(),
					"commonFingerprint" => $fingerprint->getCommonFingerprint(),
					"categoryFingerprint" => $fingerprint->getCategoryFingerprint()
				)
			)
		);

		$response = $this->elasticSearchClient->update($query);
		$this->elasticSearchClient->indices()->refresh();

		return $response["_id"];
	}


	/** getFeatureVectorDocument
	 *
	 * @param $timeSeriesId
	 * @param $indexId
	 * @return FeatureVectorDocument
	 * @throws EFeatureVectorDocumentMismatch
	 */
	public function getFeatureVectorDocument($timeSeriesId, $indexId){
		$response = $this->elasticSearchClient->search(array(
			"index" => $indexId,
			"type" => self::DOCUMENT_TYPE_FVD,
			"body" => array(
				"query" => array(
					"constant_score" => array(
						"filter" => array(
							"term" => array(
								"timeSeriesId" => $timeSeriesId
							)
						)
					)
				)
			)
		));

		if (1 === $response["hits"]["total"]){
			return $response["hits"]["hits"][0];
		}

		throw new EFeatureVectorDocumentMismatch("number of document returned for $timeSeriesId:  " . $response["hits"]["total"]);
	}

	/**
	 * @param array $fingerprint
	 * @param SearchQuery $searchQuery
	 * @param $indexName
	 * @return array
	 * @throws \Exception
	 */
	public function findNearestNeighbours(array $fingerprint, SearchQuery $searchQuery, $indexName) {
		$idealBucketHashCriteria = [];

		if ($searchQuery->getCategoryFilter()->equals(CategoryFilter::FILTER_ANY)){
			$elasticSearchFingerprint = "commonFingerprint";
		}else{
			$elasticSearchFingerprint = "categoryFingerprint";
		}

		foreach ($fingerprint as $aHashTableId => $aHash) {
			$idealBucketHashCriteria[] = array(
				"match" => array(
					"$elasticSearchFingerprint.$aHashTableId" => $aHash
				)
			);

			$idealBucketHashCriteria[] = array(
				"match" => array(
					"$elasticSearchFingerprint.$aHashTableId" => array(
						"query" => $aHash,
						"fuzziness" => 3,
						"fuzzy_transpositions" => false
					)
				)
			);
		}

		$filter = [];
		/** If we need a filter */
		if (!$searchQuery->getCategoryFilter()->equals(CategoryFilter::FILTER_ANY)
			|| count($searchQuery->getTags()) > 0 || NULL !== $searchQuery->getSource()){

			$filter = array(
				"bool" => array(
					"must" => []
				)
			);

			/** Create a boolean filter for categories */
			if (!$searchQuery->getCategoryFilter()->equals(CategoryFilter::FILTER_ANY)){
				$filter["bool"]["must"][] = array(
					"term" => array(
						"topLevelCategory" => $searchQuery->getCategoryFilter()->chosenOption()
					)
				);
			}

			/** Create a filter for each tag that we're filtering by */
			if (count($searchQuery->getTags()) > 0){
				$filter["bool"]["must"][] = array(
					"terms" => array(
						"tags" => $searchQuery->getTags()
					)
				);
			}

			if (NULL !== $searchQuery->getSource()){
				$filter["bool"]["must"][] = array(
					"term" => array(
						"sourceKeyword" => $searchQuery->getSource()->getName()
					)
				);
			}
		}

		$query = array(
			"index" => $indexName,
			"type" => self::DOCUMENT_TYPE_FVD,
			"body" => array(
				"from" => 0,
				"size" => 150,
				"query" => array(
					"bool" => array(
						"filter" => $filter,
						"must" => array(
							"nested" => array(
								"path" => $elasticSearchFingerprint,
								"query" => array(
									"bool" => array(
										"should" => $idealBucketHashCriteria
									)
								)
							)
						)
					)
				)
			)
		);

		$this->queryLog->log($query);
		$potentialNeighbours = $this->elasticSearchClient->search($query);

		if (count($potentialNeighbours["hits"]["hits"]) < 100){
			$this->sendLowNodeAlert($query);
		}

		return $potentialNeighbours;
	}

    public function search($term, $indexName, $offset, $pageSize) {
        $query = [
            "index" => $indexName,
            "type" => self::DOCUMENT_TYPE_FVD,
            "body" => [
                "from" => $offset,
                "size" => $pageSize,
                "query" => [
                    "query_string" => [
                        "query" => "*".strtolower($term)."*",
	                    "fields" => ["_all"]
                    ],
                ],
            ],
        ];

        $this->queryLog->log($query);
        $results = $this->elasticSearchClient->search($query);

        return $results;
	}

    public function removeFeatureVectorDocument($documentId, $indexName) {
        $this->elasticSearchClient->delete([
            "id" => $documentId,
            "index" => $indexName,
            "type" => self::DOCUMENT_TYPE_FVD
        ]);
    }

}