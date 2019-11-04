<?php

namespace UnitTests\DomainLayer\ORM\DomainEntity;

use DomainLayer\ORM\DomainEntity\DomainEntity;

/**
 * Class CategoryTest
 * @package UnitTests\DomainLayer\ORM\DomainEntity
 */
class DomainEntityTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var DomainEntity
     */
    private $entity;

    /** setUp
     *
     *  Set up the test class
     *
     */
    public function setUp(){
        $name = "Test Name";
        $catMock = \Mockery::mock(Category::class);
        $this->cat = new Category($name, $catMock);
    }

    /** tearDown
     *
     *  Closes and tests all Mockery assertions.
     *
     */
    public function tearDown(){
        \Mockery::close();
    }

}