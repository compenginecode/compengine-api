<?php

namespace Tests\UnitTests\InfrastructureLayer\ElasticSearch;

use DomainLayer\ORM\FeatureVector\FeatureVector;
use Elasticsearch\Client;
use InfrastructureLayer\ElasticSearch\ElasticSearch;

/**
 * Class ElasticSearchExposure
 * @package Tests\UnitTests\InfrastructureLayer\ElasticSearch
 */
class ElasticSearchExposure extends ElasticSearch{

	/**
	 * @param $indexKey
	 * @param array $featureVectorElementIds
	 * @return array
	 */
	public function getRawFeatureVectorPercentilesQuery($indexKey, array $featureVectorElementIds) {
		return parent::getRawFeatureVectorPercentilesQuery($indexKey, $featureVectorElementIds);
	}

}

/**
 * Class ElasticSearch_Test
 * @package Tests\UnitTests\InfrastructureLayer\ElasticSearch
 */
class ElasticSearch_Test extends \PHPUnit_Framework_TestCase {

	/**
	 * @throws \DI\NotFoundException
	 * @id Test1
	 */
	public function test_getSFIPercentileQuery_query_method_returns_correct_query_dsl(){
		$featureVector = new FeatureVector();
		$featureVector->addElementValue("m0", 1);
		$featureVector->addElementValue("m1", 2);

		global $container;
		/** @var ElasticSearch $elasticSearch */
		$elasticSearch = $container->get(ElasticSearchExposure::class);

		$reflectionObject = new \ReflectionObject($elasticSearch);
		$reflectedMethod = $reflectionObject->getMethod("getSFIPercentileQuery");
		$reflectedMethod->setAccessible(TRUE);

		$dsl = $reflectedMethod->invoke($elasticSearch, "indexKey", ["m0", "m1"], $featureVector);
		$this->assertEquals(json_decode(file_get_contents("test1.json"), JSON_OBJECT_AS_ARRAY), $dsl);
	}

}