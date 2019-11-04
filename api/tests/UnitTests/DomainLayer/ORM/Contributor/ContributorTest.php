<?php

namespace UnitTests\DomainLayer\ORM\Contributor;

use DomainLayer\ORM\Contributor\Contributor;

/**
 * Class ContributorTest
 * @package UnitTests\DomainLayer\ORM\Contributor
 */
class ContributorTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Contributor
     */
    private $contributor;

    /** setUp
     *
     *  Set up the test class
     *
     */
    public function setUp(){
        $name = "Test Name";
        $email = "Test Email";
        $this->contributor = new Contributor($name, $email);
        $this->contributor->setWantsAggregationEmail(TRUE);
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
        $name = "Test Name";
        $email = "Test Email";
        $contributor = new Contributor($name, $email);

        $this->assertEquals($name, $contributor->getName());
        $this->assertEquals($email, $contributor->getEmailAddress());
        $this->assertTrue($contributor instanceof Contributor);
    }

    /** test_set_wants_aggregation_email
     *
     *  Ensures the setWantsAggregationEmail method sets the wantsAggregationEmail variable
     *
     */
    public function test_set_wants_aggregation_email() {
        $this->contributor->setWantsAggregationEmail(FALSE);

        $this->assertFalse($this->contributor->wantsAggregationEmail());
    }

    /** test_get_name
     *
     *  Ensures the getName method returns the contributor name
     *
     */
    public function test_get_name() {
        $this->assertEquals("Test Name", $this->contributor->getName());
    }

    /** test_get_email_address
     *
     *  Ensures the getEmailAddress method returns the email address
     *
     */
    public function test_get_email_address() {
        $this->assertEquals("Test Email", $this->contributor->getEmailAddress());
    }

    /** test_wants_aggregation_email
     *
     *  Ensures the wantsAggregationEmail method gets the wantsAggregationEmail variable
     *
     */
    public function test_wants_aggregation_email() {
        $this->assertTrue($this->contributor->wantsAggregationEmail());
    }

}