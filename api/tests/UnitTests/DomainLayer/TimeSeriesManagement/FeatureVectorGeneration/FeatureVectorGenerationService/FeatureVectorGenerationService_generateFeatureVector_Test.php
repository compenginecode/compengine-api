<?php

namespace Tests\UnitTests\DomainLayer\TimeSeriesManagement\FeatureVectorGeneration\FeatureVectorGenerationService;

use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorGenerationService\FeatureVectorGenerationService;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\MappingManifest\MappingManifest;

/**
 * Class FeatureVectorGenerationServiceExposure
 * @package Tests\UnitTests\DomainLayer\TimeSeriesManagement\FeatureVectorGeneration\FeatureVectorGenerationService
 */
class FeatureVectorGenerationServiceExposure extends FeatureVectorGenerationService{

	public function generateFeatureVector(array $timeSeriesDataPoints, MappingManifest $mappingManifest) {
		return parent::generateFeatureVector($timeSeriesDataPoints, $mappingManifest);
	}

}

/**
 * Class FeatureVectorGenerationService_generateFeatureVector_Test
 * @package Tests\UnitTests\DomainLayer\TimeSeriesManagement\FeatureVectorGeneration\FeatureVectorGenerationService
 */
class FeatureVectorGenerationService_generateFeatureVector_Test extends \PHPUnit_Framework_TestCase {

	/** $service
	 *
	 * 	Instance of FeatureVectorGenerationServiceExposure used in this
	 * 	test suite.
	 *
	 * @var FeatureVectorGenerationServiceExposure
	 */
	private $service;

	/** getFakeFeatureVectorFamily
	 *
	 * 	Generates and returns a new feature vector family with $count
	 * 	random descriptors.
	 *
	 * @param $count
	 * @return FeatureVectorFamily
	 */
	protected function getFakeFeatureVectorFamily($count){
		$family = new FeatureVectorFamily(1, 2, 3, "php " . __DIR__ . "/Mocks/GoodScript.php", 1, 1);
		for($i = 0; $i < $count; $i++){
			$family->descriptors()->addDescriptor($i, $i);
		}

		return $family;
	}

	/** setUp
	 *
	 * 	Sets up each test case.
	 *
	 */
	public function setUp(){
		$this->service = new FeatureVectorGenerationServiceExposure();
	}

	/** test_can_instantiate_class
	 *
	 * 	Ensures that the class can be instantiated.
	 *
	 */
	public function test_can_instantiate_class(){
		$instance = new FeatureVectorGenerationServiceExposure();
		$this->assertNotNull($instance, "FeatureVectorGenerationService class can be instantiated.");
	}

	/** test_can_execute_and_parse_correct_results
	 *
	 * 	Ensures that when the manifest is met, the correct results are returned. Tests the CLI STDIN/OUT
	 * 	process.
	 *
	 */
	public function test_can_execute_and_parse_correct_results(){
		$mappingManifest = new MappingManifest($this->getFakeFeatureVectorFamily(3));

		$featureVector = $this->service->generateFeatureVector([1,2,3,4,5], $mappingManifest);
		$this->assertEquals(0, $featureVector->getElementValue("0"));
		$this->assertEquals(1, $featureVector->getElementValue("1"));
		$this->assertEquals(2, $featureVector->getElementValue("2"));
	}

	/** test_when_results_are_missing_from_script_an_exception_is_thrown
	 *
	 * 	Ensures that when the generator script fails to returns a mapping id, an exception is thrown.
	 *
	 *
	 * @expectedException \DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorGenerationService\Exceptions\EManifestNotAdheredTo
	 */
	public function test_when_results_are_missing_from_script_an_exception_is_thrown(){
		$mappingManifest = new MappingManifest($this->getFakeFeatureVectorFamily(4));

		$this->service->generateFeatureVector([1,2,3,4,5], $mappingManifest);
	}

	/** test_when_results_are_missing_from_script_an_exception_is_thrown
	 *
	 * 	Ensures that when the generator script outputs unused mapping ids, an exception is thrown.
	 *
	 *
	 * @expectedException \DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorGenerationService\Exceptions\EManifestNotAdheredTo
	 */
	public function test_when_results_are_added_from_script_an_exception_is_thrown(){
		$mappingManifest = new MappingManifest($this->getFakeFeatureVectorFamily(2));

		$this->service->generateFeatureVector([1,2,3,4,5], $mappingManifest);
	}

}