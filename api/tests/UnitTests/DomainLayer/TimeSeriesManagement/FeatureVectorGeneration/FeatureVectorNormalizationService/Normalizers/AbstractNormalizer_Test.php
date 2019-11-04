<?php

namespace Tests\UnitTests\DomainLayer\TimeSeriesManagement\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers;

use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers\AbstractNormalizer;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\MappingManifest\MappingManifest;

/**
 * Class MockNormalizer
 * @package Tests\UnitTests\DomainLayer\TimeSeriesManagement\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers
 */
class MockNormalizer extends AbstractNormalizer{

	/** getInterQuartileRange
	 *
	 * 	Returns the interquartile range of the given distribution of the given feature vector element.
	 *
	 * @param FeatureVectorFamily $featureVectorFamily
	 * @param $featureElementMappingId
	 * @return float
	 */
	protected function getInterQuartileRange(FeatureVectorFamily $featureVectorFamily, $featureElementMappingId){
		return 1;
	}

	/** getMedian
	 *
	 * 	Returns the median of the given distribution of the given feature vector element.
	 *
	 * @param FeatureVectorFamily $featureVectorFamily
	 * @param $featureElementMappingId
	 * @return float
	 */
	protected function getMedian(FeatureVectorFamily $featureVectorFamily, $featureElementMappingId){
		return 1;
	}

}

/**
 * Class MockNormalizer_Test
 * @package Tests\UnitTests\DomainLayer\TimeSeriesManagement\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers
 */
class MockNormalizer_Test extends \PHPUnit_Framework_TestCase {

	/** test_can_instantiate_class
	 *
	 * 	Ensures that the class can be instantiated.
	 *
	 */
	public function test_can_instantiate_class(){
		$instance = new MockNormalizer();
		$this->assertNotNull($instance, "MockNormalizer class can be instantiated.");
	}

	/** test_can_normalize_positive_value_correctly
	 *
	 * 	Ensures that the standard normalizer can normalize positive values correctly.
	 *
	 */
	public function test_can_normalize_positive_value_correctly(){
		/** Mock out a normalizer so we can manually specify the IQR and median */
		/** @var MockNormalizer $normalizer */
		$normalizer = \Mockery::mock(MockNormalizer::class)
			->shouldAllowMockingProtectedMethods()
			->shouldReceive(array(
				"getInterQuartileRange" => 2,
				"getMedian" => 1
			))
			->getMock()
			->makePartial();

		/** Mock out a manifest so we can specify how many feature vector elements exist */
		/** @var MappingManifest $mappingManifest */
		$mappingManifest = \Mockery::mock(MappingManifest::class)
			->shouldReceive(array(
				"featureElementMappingIds" => [1],
				"getFeatureVectorFamily" => new FeatureVectorFamily("1", "2", "3", "4", 1, 1)
			))
			->getMock()
			->makePartial();

		/** Create a raw feature vector */
		$rawFeatureVector = new FeatureVector();
		$rawFeatureVector->addElementValue(1, 5);

		/** Now we normalize $rawFeatureVector and check the results  */
		$expectedResult = 1.481;
		$normalizedFeatureVector = $normalizer->normalize($mappingManifest, $rawFeatureVector);
		$this->assertEquals($expectedResult, round($normalizedFeatureVector->getElementValue(1), 3));
	}

	/** test_can_normalize_negative_value_correctly
	 *
	 * 	Ensures that the standard normalizer can normalize negative values correctly.
	 *
	 */
	public function test_can_normalize_negative_value_correctly(){
		/** Mock out a normalizer so we can manually specify the IQR and median */
		/** @var MockNormalizer $normalizer */
		$normalizer = \Mockery::mock(MockNormalizer::class)
			->shouldAllowMockingProtectedMethods()
			->shouldReceive(array(
				"getInterQuartileRange" => 2,
				"getMedian" => 1
			))
			->getMock()
			->makePartial();

		/** Mock out a manifest so we can specify how many feature vector elements exist */
		/** @var MappingManifest $mappingManifest */
		$mappingManifest = \Mockery::mock(MappingManifest::class)
			->shouldReceive(array(
				"featureElementMappingIds" => [1],
				"getFeatureVectorFamily" => new FeatureVectorFamily("1", "2", "3", "4", 1, 1)
			))
			->getMock()
			->makePartial();

		/** Create a raw feature vector */
		$rawFeatureVector = new FeatureVector();
		$rawFeatureVector->addElementValue(1, -5);

		/** Now we normalize $rawFeatureVector and check the results  */
		$expectedResult = -2.222;
		$normalizedFeatureVector = $normalizer->normalize($mappingManifest, $rawFeatureVector);
		$this->assertEquals($expectedResult, round($normalizedFeatureVector->getElementValue(1), 3));
	}

}