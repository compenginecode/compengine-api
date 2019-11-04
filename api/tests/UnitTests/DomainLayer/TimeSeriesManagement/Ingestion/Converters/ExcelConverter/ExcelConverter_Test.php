<?php

namespace Tests\UnitTests\DomainLayer\TimeSeriesManagement\Ingestion\Converters\CSVConverter;

use DomainLayer\TimeSeriesManagement\Ingestion\Converters\ExcelConverter\ExcelConverter;

/**
 * Class ExcelConverterTest
 * @package Tests\UnitTests\DomainLayer\TimeSeriesManagement\Ingestion\Converters\CSVConverter
 */
class ExcelConverterTest extends \PHPUnit_Framework_TestCase {

	/** $converter
	 *
	 * 	Instance of CSVConverterExposure used in this
	 * 	test suite.
	 *
	 * @var ExcelConverter
	 */
	private $converter;

	/** setUp
	 *
	 * 	Sets up each test case.
	 *
	 */
	public function setUp(){
		$this->converter = new ExcelConverter();
	}

	/** test_can_instantiate_class
	 *
	 * 	Ensures that the class can be instantiated.
	 *
	 */
	public function test_can_instantiate_class(){
		$instance = new CSVConverterExposure();
		$this->assertNotNull($instance, "ExcelConverter class can be instantiated.");
	}

	/** test_can_convert_excel_file_with_no_header_to_time_series
	 *
	 * 	Ensures that the Excel converter can convert an Excel file (with no header) into a
	 * 	time series correctly.
	 *
	 */
	public function test_can_convert_excel_file_with_no_header_to_time_series(){
		$mockFile = __DIR__ . "/Mocks/NoHeader.xlsx";
		$expected = implode(",", [1,2,3,4]);
		$response = implode(",", $this->converter->convertToTimeSeries($mockFile));

		$this->assertEquals($expected, $response, "ExcelConverter converts a standard Excel file with no header correctly.");
	}

	/** test_can_convert_excel_file_with_header_to_time_series
	 *
	 * 	Ensures that the Excel converter can convert an Excel file (with a header) into a
	 * 	time series correctly.
	 *
	 */
	public function test_can_convert_excel_file_with_header_to_time_series(){
		$mockFile = __DIR__ . "/Mocks/WithHeader.xlsx";
		$expected = implode(",", [1,2,3,4]);
		$response = implode(",", $this->converter->convertToTimeSeries($mockFile));

		$this->assertEquals($expected, $response, "ExcelConverter converts a standard Excel file with a header correctly.");
	}

	/** test_can_convert_old_excel_1997_file_to_time_series
	 *
	 * 	Ensures that the Excel converter can convert an old Excel 1997 file correctly.
	 *
	 */
	public function test_can_convert_old_excel_1997_file_to_time_series(){
		$mockFile = __DIR__ . "/Mocks/OldExcelFormat.xls";
		$expected = implode(",", [1,2,3,4]);
		$response = implode(",", $this->converter->convertToTimeSeries($mockFile));

		$this->assertEquals($expected, $response, "ExcelConverter converts an old Excel 1997 file correctly.");
	}

}