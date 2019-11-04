<?php

namespace UnitTests\DomainLayer\ORM\Source;

use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\Source\Source;

/**
 * Class SourceTest
 * @package UnitTests\DomainLayer\ORM\Source
 */
class SourceTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Source
     */
    private $src;

    /** setUp
     *
     *  Set up the test class
     *
     */
    public function setUp(){
        $name = "Test Name";
        $this->src = new Source($name);
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
        $src = new Source($name);

        $this->assertEquals($name, $src->getName());
        $this->assertEquals(ApprovalStatus::UNAPPROVED, $src->getApprovalStatus()->chosenOption());
        $this->assertTrue($src instanceof Source);
    }

    /** test_get_name
     *
     *  Ensures the getName method returns the name
     *
     */
    public function test_get_name() {
        $name = "Test Name";

        $this->assertEquals($name, $this->src->getName());
    }

    /** test_get_approval_status
     *
     *  Ensures the getApprovalStatus method returns the approval status
     *
     */
    public function test_get_approval_status() {
        $this->assertEquals(ApprovalStatus::UNAPPROVED, $this->src->getApprovalStatus()->chosenOption());
    }

}