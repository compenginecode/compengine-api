<?php

namespace Tests\UnitTests\DomainLayer\TimeSeriesManagement\Ingestion\Converters\CSVConverter;
use DomainLayer\TimeSeriesManagement\Ingestion\Converters\WavConverter\WavConverter;

/**
 * Class WavConverterTest
 * @package Tests\UnitTests\DomainLayer\TimeSeriesManagement\Ingestion\Converters\CSVConverter
 */
class WavConverterTest extends \PHPUnit_Framework_TestCase {

	/** $converter
	 *
	 * 	Instance of CSVConverterExposure used in this
	 * 	test suite.
	 *
	 * @var WavConverter
	 */
	private $converter;

	/** setUp
	 *
	 * 	Sets up each test case.
	 *
	 */
	public function setUp(){
		$this->converter = new WavConverter();
	}

	/** test_can_instantiate_class
	 *
	 * 	Ensures that the class can be instantiated.
	 *
	 */
	public function test_can_instantiate_class(){
		$instance = new CSVConverterExposure();
		$this->assertNotNull($instance, "WavConverter class can be instantiated.");
	}

	/** test_can_convert_wav_to_time_series
	 *
	 * 	Ensures that the Wav converter can convert an Wav file into a
	 * 	time series correctly.
	 *
	 */
	public function test_can_convert_wav_to_time_series(){
		$mockAudio = __DIR__ . "/Mocks/Bass.wav";
		$response = $this->converter->convertToTimeSeries($mockAudio);
		$this->assertCount(3736, $response, "WavConverter converts a standard bear sound correctly.");
	}

}