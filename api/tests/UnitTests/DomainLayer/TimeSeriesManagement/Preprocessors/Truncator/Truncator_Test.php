<?php

namespace Tests\UnitTests\DomainLayer\TimeSeriesManagement\Ingestion\Preprocessors\Truncator;

use DomainLayer\TimeSeriesManagement\Ingestion\Preprocessors\Truncator\Truncator;

/**
 * Class TruncatorExposure
 * @package Tests\UnitTests\DomainLayer\TimeSeriesManagement\Ingestion\Preprocessors\Truncator
 */
class TruncatorExposure extends Truncator{

	/** getTruncationLimit
	 *
	 * 	Override so we get a smaller number - easier to test!
	 *
	 * @return int
	 */
	protected function getTruncationLimit(){
		return 5;
	}

	/** getOriginalTruncationLimit
	 *
	 * 	We'll test default value.
	 *
	 * @return int
	 */
	public function getOriginalTruncationLimit(){
		return parent::getTruncationLimit();
	}

}

/**
 * Class TruncatorTest
 * @package Tests\UnitTests\DomainLayer\TimeSeriesManagement\Ingestion\Preprocessors\Truncator
 */
class TruncatorTest extends \PHPUnit_Framework_TestCase {

	/** $truncator
	 *
	 * 	Instance of Truncator used in this
	 * 	test suite.
	 *
	 * @var TruncatorExposure
	 */
	private $truncator;

	/** setUp
	 *
	 * 	Sets up each test case.
	 *
	 */
	public function setUp(){
		$this->truncator = new TruncatorExposure();
	}

	/** test_can_instantiate_class
	 *
	 * 	Ensures that the class can be instantiated.
	 *
	 */
	public function test_can_instantiate_class(){
		$instance = new Truncator();
		$this->assertNotNull($instance, "Truncator class can be instantiated.");
	}

	/** test_truncate_works_on_empty_array
	 *
	 * 	Ensures that the truncation works correctly when passed an
	 * 	empty array.
	 *
	 */
	public function test_truncate_works_on_empty_array(){
		$result = $this->truncator->preProcessTimeSeries([]);
		$this->assertEquals(0, count($result), "Truncator class correctly truncates an empty array.");
	}


	/** test_truncate_correctly_truncates_long_array
	 *
	 * 	Ensures that the truncator correctly truncates a long array.
	 *
	 */
	public function test_truncate_correctly_truncates_long_array(){
		$array = [1,2,3,4,5,6];
		$expected = [1,2,3,4,5];

		$result = $this->truncator->preProcessTimeSeries($array);
		$this->assertEquals(implode(",", $expected), implode(",", $result), "Truncator class correctly truncates a long array.");
	}

	/** test_truncate_correctly_truncates_long_array
	 *
	 * 	Ensures that the truncator correctly truncates a short array.
	 *
	 */
	public function test_truncate_correctly_truncates_short_array(){
		$array = [1,2,3];
		$expected = [1,2,3];

		$result = $this->truncator->preProcessTimeSeries($array);
		$this->assertEquals(implode(",", $expected), implode(",", $result), "Truncator class correctly truncates a short array.");
	}

	/** test_truncate_limit_is_correct
	 *
	 * 	Ensures that the truncation limit is correct.
	 *
	 */
	public function test_truncate_limit_is_correct(){
		$expected = 10000;
		$result = $this->truncator->getOriginalTruncationLimit();

		$this->assertEquals($expected, $result, "Truncator class has correct truncation limit.");
	}

	/** test_correctly_determines_if_truncation_required
	 *
	 * 	Ensures that the truncation class correctly determines if truncation is required or
	 * 	not over a series of test inputs.
	 *
	 */
	public function test_correctly_determines_if_truncation_required(){
		$tests = array(
			[[], FALSE],
			[[1,2,3,4,5], FALSE],
			[[1,2,3,4,5,6], TRUE]
		);

		foreach($tests as $aTest){
			$this->assertEquals($aTest[1], $this->truncator->requiresPreprocessing($aTest[0]),
				"Truncator class correctly determines truncation requirements for array: " . implode(",", $aTest[0]) . ".");
		}
	}

}