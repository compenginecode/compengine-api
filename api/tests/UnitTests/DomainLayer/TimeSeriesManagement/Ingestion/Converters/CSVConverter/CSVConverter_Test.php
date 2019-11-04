<?php

namespace Tests\UnitTests\DomainLayer\TimeSeriesManagement\Ingestion\Converters\CSVConverter;

use DomainLayer\TimeSeriesManagement\Ingestion\Converters\CSVConverter\CSVConverter;

/**
 * Class CSVConverterExposure
 * @package DomainLayer\TimeSeriesManagement\Ingestion\Converters\Exceptions
 */
class CSVConverterExposure extends CSVConverter{

	/** detectDelimiter
	 *
	 * 	Expose the protected method publically for testing.
	 *
	 * @param $filePath
	 * @return array
	 */
	public function detectDelimiter($filePath) {
		return parent::detectDelimiter($filePath);
	}

}

/**
 * Class CSVConverterTest
 * @package Tests\UnitTests\DomainLayer\TimeSeriesManagement\Ingestion\Converters\CSVConverter
 */
class CSVConverterTest extends \PHPUnit_Framework_TestCase {

	/** $converter
	 *
	 * 	Instance of CSVConverterExposure used in this
	 * 	test suite.
	 *
	 * @var CSVConverterExposure
	 */
	private $converter;

	/** setUp
	 *
	 * 	Sets up each test case.
	 *
	 */
	public function setUp(){
		$this->converter = new CSVConverterExposure();
	}

	/** test_can_instantiate_class
	 *
	 * 	Ensures that the class can be instantiated.
	 *
	 */
	public function test_can_instantiate_class(){
		$instance = new CSVConverterExposure();
		$this->assertNotNull($instance, "CSVConverter class can be instantiated.");
	}

	/** test_can_detect_line_feed_line_break_correctly
	 *
	 * 	Ensures that the correct delimiter is chosen. In this particular case,
	 * 	we want the LF ASCII character (#10) to be detected.
	 *
	 */
	public function test_can_detect_line_feed_line_break_correctly(){
		$exampleFilePath = __DIR__ . "/Mocks/LineFeedExample.dat";
		$delimiter = $this->converter->detectDelimiter(file_get_contents($exampleFilePath));

		$this->assertEquals("\n", $delimiter, "CSVConverter detects line feed return line break");
	}

	/** test_can_detect_line_feed_carriage_return_line_break_correctly
	 *
	 * 	Ensures that the correct delimiter is chosen. In this particular case,
	 * 	we want the LF and CR ASCII characters (#10, #13) to be detected.
	 *
	 */
	public function test_can_detect_line_feed_carriage_return_line_break_correctly(){
		$exampleFilePath = __DIR__ . "/Mocks/LineFeedCarriageReturnExample.dat";
		$delimiter = $this->converter->detectDelimiter(file_get_contents($exampleFilePath));

		$this->assertEquals("\n", $delimiter, "CSVConverter detects line feed+carriage return line break");
	}

	/** test_can_detect_comma_line_break_correctly
	 *
	 * 	Ensures that the correct delimiter is chosen. In this particular case,
	 * 	we want the comma ASCII character to be detected.
	 *
	 */
	public function test_can_detect_comma_line_break_correctly(){
		$exampleFilePath = __DIR__ . "/Mocks/CommaExample.dat";
		$delimiter = $this->converter->detectDelimiter(file_get_contents($exampleFilePath));

		$this->assertEquals(",", $delimiter, "CSVConverter detects comma line break");
	}

	/** test_can_detect_tab_line_break_correctly
	 *
	 * 	Ensures that the correct delimiter is chosen. In this particular case,
	 * 	we want the tab ASCII character to be detected.
	 *
	 */
	public function test_can_detect_tab_line_break_correctly(){
		$exampleFilePath = __DIR__ . "/Mocks/TabExample.dat";
		$delimiter = $this->converter->detectDelimiter(file_get_contents($exampleFilePath));

		$this->assertEquals("\t", $delimiter, "CSVConverter detects tab line break");
	}

	/** test_can_detect_tab_line_break_correctly
	 *
	 * 	Ensures that when no delimiter is found at all, an exception is thrown.
	 *
	 * @expectedException \DomainLayer\TimeSeriesManagement\Ingestion\Converters\Exceptions\EParseConversionError
	 */
	public function test_throws_exception_on_invalid_delimiter(){
		$exampleFilePath = __DIR__ . "/Mocks/PipeExample.dat";
		$this->converter->detectDelimiter(file_get_contents($exampleFilePath));
	}

	/** test_can_create_array_from_csv_with_tab_delimiter
	 *
	 * 	Ensures that once the correct tab delimiter has been chosen, it can be used
	 * 	properly to create an array of parts.
	 *
	 */
	public function test_can_create_array_from_csv_with_tab_delimiter(){
		$exampleFilePath = __DIR__ . "/Mocks/TabExample.dat";

		$expected = array(
			"-0.17051",
			"-0.32005",
			"0.2769",
			"0.1497",
			"-0.10382",
			"-0.17977",
			"0.082562"
		);

		$result = $this->converter->convertToTimeSeries($exampleFilePath);
		$this->assertEquals($expected, $result, "CSVConverter can create an array of results with a tab delimiter.");
	}

	/** test_can_create_array_from_csv_with_tab_delimiter
	 *
	 * 	Ensures that once the correct line feed delimiter has been chosen, it can be used
	 * 	properly to create an array of parts.
	 *
	 */
	public function test_can_create_array_from_csv_with_line_feed_delimiter(){
		$exampleFilePath = __DIR__ . "/Mocks/LineFeedExample.dat";

		$expected = array(
			"-0.17051",
			"-0.32005",
			"0.2769",
			"0.1497",
			"-0.10382",
			"-0.17977",
			"0.082562"
		);

		$result = $this->converter->convertToTimeSeries($exampleFilePath);
		$this->assertEquals(implode(",", $expected), implode(",",$result),
			"CSVConverter can create an array of results with a line feed delimiter.");
	}

	/** test_can_create_array_from_csv_with_line_feed_carriage_return_delimiter
	 *
	 * 	Ensures that once the correct line feed + carriage return delimiter has been chosen, it can be used
	 * 	properly to create an array of parts.
	 *
	 */
	public function test_can_create_array_from_csv_with_line_feed_carriage_return_delimiter(){
		$exampleFilePath = __DIR__ . "/Mocks/LineFeedExample.dat";

		$expected = array(
			"-0.17051",
			"-0.32005",
			"0.2769",
			"0.1497",
			"-0.10382",
			"-0.17977",
			"0.082562"
		);

		$result = $this->converter->convertToTimeSeries($exampleFilePath);
		$this->assertEquals(implode(",", $expected), implode(",",$result),
			"CSVConverter can create an array of results with a line feed/carriage return delimiter.");
	}

	/** test_non_numeric_values_are_omitted
	 *
	 * 	Ensures that non-numeric entries are omitted.
	 *
	 */
	public function test_non_numeric_values_are_omitted(){
		$exampleFilePath = __DIR__ . "/Mocks/NonNumericCSV.dat";

		$expected = array(
			"-0.17051",
			"0.1497",
			"-0.17977",
			"0.082562"
		);

		$result = $this->converter->convertToTimeSeries($exampleFilePath);
		$this->assertEquals(implode(",", $expected), implode(",",$result),
			"CSVConverter omits non-numeric entries.");
	}

}