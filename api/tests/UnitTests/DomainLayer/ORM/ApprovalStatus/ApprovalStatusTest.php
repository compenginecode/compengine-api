<?php

namespace UnitTests\DomainLayer\ORM\ApprovalStatus;

use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;

/**
 * Class ApprovalStatusTest
 * @package UnitTests\DomainLayer\ORM\ApprovalStatus
 */
class ApprovalStatusTest extends \PHPUnit_Framework_TestCase {

    /** tearDown
     *
     *  Closes and tests all Mockery assertions.
     *
     */
    public function tearDown(){
        \Mockery::close();
    }

    /** test_unapproved
     *
     *  Ensures the unapproved method returns UNAPPROVED type
     *
     */
    public function test_unapproved() {
        $status = ApprovalStatus::unapproved();
        $this->assertEquals(
            ApprovalStatus::UNAPPROVED,
            $status->chosenOption()
        );
    }

    /** test_approved
     *
     *  Ensures the approved method returns APPROVED type
     *
     */
    public function test_approved() {
        $status = ApprovalStatus::approved();
        $this->assertEquals(
            ApprovalStatus::APPROVED,
            $status->chosenOption()
        );
    }

}