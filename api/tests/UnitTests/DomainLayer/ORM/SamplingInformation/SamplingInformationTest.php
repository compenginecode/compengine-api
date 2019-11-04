<?php

namespace UnitTests\DomainLayer\ORM\SamplingInformation;

use DomainLayer\ORM\SamplingInformation\SamplingInformation;

/**
 * Class SamplingInformationTest
 * @package UnitTests\DomainLayer\ORM\SamplingInformation
 */
class SamplingInformationTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var SamplingInformation
     */
    private $info;

    /** setUp
     *
     *  Set up the test class
     *
     */
    public function setUp(){
        $enum = SamplingInformation::SAMPLING_DEFINED;
        $rate = "Test Rate";
        $unit = "Test Unit";
        $this->info = new SamplingInformation($enum, $rate, $unit);
    }

    /** tearDown
     *
     *  Closes and tests all Mockery assertions.
     *
     */
    public function tearDown(){
        \Mockery::close();
    }

    /** test_constructor
     *
     *  Ensures the object is constructed correctly
     *
     */
    public function test_constructor() {
        $enum = SamplingInformation::SAMPLING_DEFINED;
        $rate = "Test Rate";
        $unit = "Test Unit";
        $info = new SamplingInformation($enum, $rate, $unit);

        $this->assertEquals($enum, $info->chosenOption());
        $this->assertEquals($rate, $info->getSamplingRate());
        $this->assertEquals($unit, $info->getSamplingUnit());
        $this->assertTrue($info instanceof SamplingInformation);
    }

    /** test_not_defined
     *
     *  Ensures the notDefined method returns an enum with SAMPLING_NOT_DEFINED type
     *
     */
    public function test_not_defined() {
        $enum = SamplingInformation::notDefined();

        $this->assertEquals($enum->chosenOption(), SamplingInformation::SAMPLING_NOT_DEFINED);
    }

    /** test_defined
     *
     *  Ensures the defined method returns a defined SamplingInformation object
     *
     */
    public function test_defined() {
        $enum = SamplingInformation::SAMPLING_DEFINED;
        $rate = "Test Rate";
        $unit = "Test Unit";
        $defined = SamplingInformation::defined($rate, $unit);

        $this->assertEquals($enum, $defined->chosenOption());
        $this->assertEquals($rate, $defined->getSamplingRate());
        $this->assertEquals($unit, $defined->getSamplingUnit());
        $this->assertTrue($defined instanceof SamplingInformation);
    }

    /** test_get_sampling_unit
     *
     *  Ensures the getSamplingUnit method returns the sampling unit
     *
     */
    public function test_get_sampling_unit() {
        $unit = "Test Unit";

        $this->assertEquals($unit, $this->info->getSamplingUnit());
    }

    /** test_get_sampling_rate
     *
     *  Ensures the getSamplingRate method returns the sampling rate
     *
     */
    public function test_get_sampling_rate() {
        $rate = "Test Rate";

        $this->assertEquals($rate, $this->info->getSamplingRate());
    }

}