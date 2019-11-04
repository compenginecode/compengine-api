<?php

namespace Tests\UnitTests\DomainLayer\TimeSeriesManagement\Ingestion\Converters\CSVConverter;
use DomainLayer\TimeSeriesManagement\Ingestion\Converters\MP3Converter\MP3Converter;

/**
 * Class MP3onverterTest
 * @package Tests\UnitTests\DomainLayer\TimeSeriesManagement\Ingestion\Converters\CSVConverter
 */
class MP3onverterTest extends \PHPUnit_Framework_TestCase {

	/** $converter
	 *
	 * 	Instance of CSVConverterExposure used in this
	 * 	test suite.
	 *
	 * @var MP3Converter
	 */
	private $converter;

	/** setUp
	 *
	 * 	Sets up each test case.
	 *
	 */
	public function setUp(){
		$this->converter = new MP3Converter();
	}

	/** test_can_instantiate_class
	 *
	 * 	Ensures that the class can be instantiated.
	 *
	 */
	public function test_can_instantiate_class(){
		$instance = new CSVConverterExposure();
		$this->assertNotNull($instance, "MP3Converter class can be instantiated.");
	}

	/** test_can_convert_mp3_to_time_series
	 *
	 * 	Ensures that the MP3 converter can convert an MP3 file into a
	 * 	time series correctly.
	 *
	 */
	public function test_can_convert_mp3_to_time_series(){
		$mockAudio = __DIR__ . "/Mocks/Bear.mp3";
		$response = $this->converter->convertToTimeSeries($mockAudio);
		$this->assertCount(3666, $response, "MP3Converter converts a standard bear sound correctly.");
	}

}