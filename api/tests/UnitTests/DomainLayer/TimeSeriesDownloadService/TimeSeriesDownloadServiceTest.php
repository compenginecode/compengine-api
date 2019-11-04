<?php

namespace UnitTests\DomainLayer\TimeSeriesDownloadService;
use DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService\TimeSeriesDownloadService;

/**
 * Class TimeSeriesDownloadServiceTest
 * @package UnitTests\DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService
 */
class TimeSeriesDownloadServiceTest extends \PHPUnit_Framework_TestCase {

    /** @var TimeSeriesDownloadService $testObject */
    private $testObject;

    /** setUp
     *
     *  Sets up the object to be tested.
     *
     */
    public function setUp() {
        global $container;
        $this->testObject = $container->get(TimeSeriesDownloadService::class);
    }

    /** tearDown
     *
     *  Closes and tests all Mockery assertions.
     *
     */
    public function tearDown() {
        \Mockery::close();
    }

    /** test_construct
     *
     *  Ensures that the constructor constructs the object properly
     *
     */
    public function test_construct() {

    }

    /** test_
     *
     *  Ensures that the method returns
     *
     */
    public function test_generateTimeSeriesAsJson() {
        $this->testObject->generateTimeSeriesAsJson();
    }

    /** test_
     *
     *  Ensures that the method returns
     *
     */
    public function test_generateTimeSeriesMetadataAsCsv() {
        $this->testObject->generateTimeSeriesMetadataAsCsv();
    }

    /** test_
     *
     *  Ensures that the method returns
     *
     */
    public function test_generateTimeSeriesDatapointsAsCsv() {
        $this->testObject->generateTimeSeriesDatapointsAsCsv();
    }

    /** test_
     *
     *  Ensures that the method returns
     *
     */
    public function test_generateTimeSeriesAsCsv() {
        $this->testObject->generateTimeSeriesAsCsv();
    }

    public function test_getDirContents() {
        print_r($this->testObject->getDirContents("C:\\Vokke\\imperial-college-api\\private\\temp\\time-series"));
    }

    public function test_getLatestTimeSeries() {
        echo $this->testObject->getLatestTimeSeries("csv");
    }

}