<?php

namespace Tests\UnitTests\DomainLayer\TimeSeriesManagement\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers\ElasticSearchNormalizer;

use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use Elasticsearch\Client;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers\ElasticSearchNormalizer\ElasticSearchNormalizer;
use InfrastructureLayer\ElasticSearch\ElasticSearch;

/**
 * Class ElasticSearchNormalizerExposure
 * @package Tests\UnitTests\DomainLayer\TimeSeriesManagement\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers\ElasticSearchNormalizer
 */
class ElasticSearchNormalizerExposure extends ElasticSearchNormalizer{

	public function getInterQuartileRange(FeatureVectorFamily $featureVectorFamily, $featureElementMappingId) {
		return parent::getInterQuartileRange($featureVectorFamily, $featureElementMappingId);
	}

}

/**
 * Class ElasticSearchNormalizer_Test
 * @package Tests\UnitTests\DomainLayer\TimeSeriesManagement\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers\ElasticSearchNormalizer
 */
class ElasticSearchNormalizer_Test extends \PHPUnit_Framework_TestCase {

	/** test_can_instantiate_class
	 *
	 * 	Ensures that the class can be instantiated.
	 *
	 */
	public function test_can_instantiate_class(){
		/** @var ElasticSearch $elasticSearchClient */
		$elasticSearchClient = \Mockery::mock(ElasticSearch::class);
		$instance = new ElasticSearchNormalizer($elasticSearchClient);
		$this->assertNotNull($instance, "ElasticSearchNormalizer class can be instantiated.");
	}

}