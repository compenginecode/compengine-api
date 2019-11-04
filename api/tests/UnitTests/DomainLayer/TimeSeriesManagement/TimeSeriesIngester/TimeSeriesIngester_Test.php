<?php

namespace Tests\UnitTests\DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester;

use DomainLayer\TimeSeriesManagement\Ingestion\Converters\CSVConverter\CSVConverter;
use DomainLayer\TimeSeriesManagement\Ingestion\Converters\ExcelConverter\ExcelConverter;
use DomainLayer\TimeSeriesManagement\Ingestion\Converters\MP3Converter\MP3Converter;
use DomainLayer\TimeSeriesManagement\Ingestion\Converters\WavConverter\WavConverter;
use DomainLayer\TimeSeriesManagement\Ingestion\Preprocessors\Truncator\Truncator;
use DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester\TimeSeriesIngester;

/**
 * Class TimeSeriesIngesterExposure
 * @package Tests\UnitTests\DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester
 */
class TimeSeriesIngesterExposure extends TimeSeriesIngester{

	/** getConverter
	 *
	 * 	Returns the converter. Overridden and exposed as a public method for testing.
	 *
	 * @param $filePath
	 * @return \DomainLayer\TimeSeriesManagement\Ingestion\Converters\IConverter|null
	 * @throws \DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester\Exceptions\EUnsupportedFileExtension
	 */
	public function getConverter($filePath) {
		return parent::getConverter($filePath);
	}

}

/**
 * Class TimeSeriesIngesterTest
 * @package Tests\UnitTests\DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester
 */
class TimeSeriesIngesterTest extends \PHPUnit_Framework_TestCase {

	/** $timeSeriesIngester
	 *
	 * 	Instance of TimeSeriesIngester used in this
	 * 	test suite.
	 *
	 * @var TimeSeriesIngesterExposure
	 */
	private $timeSeriesIngester;

	/** setUp
	 *
	 * 	Sets up each test case.
	 *
	 */
	public function setUp(){
		global $container;
		$this->timeSeriesIngester = $container->get(TimeSeriesIngesterExposure::class);
	}

	/** test_can_instantiate_class
	 *
	 * 	Ensures that the class can be instantiated.
	 *
	 */
	public function test_can_instantiate_class(){
		global $container;

		$instance = $container->get(TimeSeriesIngesterExposure::class);
		$this->assertNotNull($instance, "TimeSeriesIngester class can be instantiated.");
	}

	/** test_gets_correct_converter_for_mp3_file
	 *
	 * 	Ensures that when supplied with an MP3 file, the class correctly decides to
	 *  use the MP3 converter class.
	 *
	 */
	public function test_gets_correct_converter_for_mp3_file(){
		$expected = get_class(new MP3Converter());
		$result = get_class($this->timeSeriesIngester->getConverter("Test.mp3"));

		$this->assertEquals($expected, $result,
			"TimeSeriesIngester class correctly chooses IConverter class for a file of type MP3.");
	}

	/** test_gets_correct_converter_for_wav_file
	 *
	 * 	Ensures that when supplied with an Wav file, the class correctly decides to
	 *  use the WAV converter class.
	 *
	 */
	public function test_gets_correct_converter_for_wav_file(){
		$expected = get_class(new WavConverter());
		$result = get_class($this->timeSeriesIngester->getConverter("Test.wav"));

		$this->assertEquals($expected, $result,
			"TimeSeriesIngester class correctly chooses IConverter class for a file of type WAV.");
	}

	/** test_gets_correct_converter_for_csv_file
	 *
	 * 	Ensures that when supplied with an Csv file, the class correctly decides to
	 *  use the CSV converter class.
	 *
	 */
	public function test_gets_correct_converter_for_csv_file(){
		$expected = get_class(new CSVConverter());
		$result = get_class($this->timeSeriesIngester->getConverter("Test.csv"));

		$this->assertEquals($expected, $result,
			"TimeSeriesIngester class correctly chooses IConverter class for a file of type CSV.");
	}

	/** test_gets_correct_converter_for_txt_file
	 *
	 * 	Ensures that when supplied with an TXT file, the class correctly decides to
	 *  use the TXT converter class.
	 *
	 */
	public function test_gets_correct_converter_for_txt_file(){
		$expected = get_class(new CSVConverter());
		$result = get_class($this->timeSeriesIngester->getConverter("Test.txt"));

		$this->assertEquals($expected, $result,
			"TimeSeriesIngester class correctly chooses IConverter class for a file of type TXT.");
	}

	/** test_gets_correct_converter_for_excel_file
	 *
	 * 	Ensures that when supplied with an XLSX file, the class correctly decides to
	 *  use the TXT converter class.
	 *
	 */
	public function test_gets_correct_converter_for_excel_file(){
		$expected = get_class(new ExcelConverter());
		$result = get_class($this->timeSeriesIngester->getConverter("Test.xlsx"));

		$this->assertEquals($expected, $result,
			"TimeSeriesIngester class correctly chooses IConverter class for a file of type XLSX.");
	}

	/** test_exception_thrown_for_unsupported_file_types
	 *
	 * 	Ensures that when supplied with an unsupported file type, an exception is thrown
	 *
	 * @expectedException \DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester\Exceptions\EUnsupportedFileExtension
	 */
	public function test_exception_thrown_for_unsupported_file_types(){
		$this->timeSeriesIngester->getConverter("Test.strangeFileType");
	}

}