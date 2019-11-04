<?php

namespace UnitTests\DomainLayer\ORM\Category\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\Category\Repository\DatabaseCategoryRepository;

/**
 * Class DatabaseCategoryRepositoryTest
 * @package UnitTests\DomainLayer\ORM\Category\Repository
 */
class DatabaseCategoryRepositoryTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var DatabaseCategoryRepository
     */
    private $repo;

//    /** setUp
//     *
//     *  Set up the test class
//     *
//     */
//    public function setUp(){
//        $emMock = \Mockery::mock(EntityManager::class);
//        $metaDataMock = \Mockery::mock(ClassMetadata::class);
//
//        $this->repo = new DatabaseCategoryRepository($emMock, $metaDataMock);
//    }

    /** tearDown
     *
     *  Closes and tests all Mockery assertions.
     *
     */
    public function tearDown(){
        \Mockery::close();
    }

    public function test_find_by_id() {
        $catMock = \Mockery::mock(Category::class);
        $emMock = \Mockery::mock(EntityManager::class)
            ->shouldReceive('findOneBy')
            ->andReturn(new Category("Test Name", $catMock))
            ->getMock();
        $metaDataMock = \Mockery::mock(ClassMetadata::class);

        $repo = new DatabaseCategoryRepository($emMock, $metaDataMock);
        $cat = $repo->findById(12);

        $this->assertTrue($cat instanceof Category);
    }
}