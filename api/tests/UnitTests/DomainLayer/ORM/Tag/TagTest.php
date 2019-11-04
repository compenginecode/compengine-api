<?php

namespace UnitTests\DomainLayer\ORM\Tag;

use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\Tag\Tag;

/**
 * Class TagTest
 * @package UnitTests\DomainLayer\ORM\Tag
 */
class TagTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Tag
     */
    private $tag;

    /** setUp
     *
     *  Set up the test class
     *
     */
    public function setUp(){
        $name = "Test Name";
        $this->tag = new Tag($name);
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
        $tag = new Tag($name);

        $this->assertEquals($name, $tag->getName());
        $this->assertEquals(ApprovalStatus::UNAPPROVED, $tag->getApprovalStatus()->chosenOption());
        $this->assertTrue($tag instanceof Tag);
    }

    /** test_get_name
     *
     *  Ensures the getName method returns the name
     *
     */
    public function test_get_name() {
        $name = "Test Name";

        $this->assertEquals($name, $this->tag->getName());
    }

    /** test_get_approval_status
     *
     *  Ensures the getApprovalStatus method returns the approval status
     *
     */
    public function test_get_approval_status() {
        $this->assertEquals(ApprovalStatus::UNAPPROVED, $this->tag->getApprovalStatus()->chosenOption());
    }

}