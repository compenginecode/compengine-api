<?php

namespace UnitTests\DomainLayer\ORM\Category;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\Category\Category;
use Mockery\Mock;

/**
 * Class CategoryTest
 * @package UnitTests\DomainLayer\ORM\Category
 */
class CategoryTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Category
     */
    private $cat;

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

    /** test_constructor
     *
     *  Ensures the object is constructed correctly
     *
     */
    public function test_constructor() {
        $name = "Test Name";
        $catMock = \Mockery::mock(Category::class);
        $cat = new Category($name, $catMock);

        $this->assertEquals($name, $cat->getName());
        $this->assertTrue($cat->getParentCategory() instanceof Category);
        $this->assertTrue($cat instanceof Category);
    }

    /** test_has_parent_true
     *
     *  Ensures the hasParent method returns true if parent is set
     *
     */
    public function test_has_parent_true() {
        $this->assertTrue($this->cat->hasParent());
    }

    /** test_has_parent_false
     *
     *  Ensures the hasParent method returns false if parent is null
     *
     */
    public function test_has_parent_false() {
        $cat = new Category("Test Name", NULL);
        $this->assertFalse($cat->hasParent());
    }

    /** test_get_parent_category
     *
     *  Ensures the getParentCategory method returns a category
     *
     */
    public function test_get_parent_category() {
        $this->assertTrue($this->cat->getParentCategory() instanceof Category);
    }

    /** test_get_name
     *
     *  Ensures the getName method returns the category name
     *
     */
    public function test_get_name() {
        $this->assertEquals("Test Name", $this->cat->getName());
    }

    /** test_get_approval_status
     *
     *  Ensures the getApprovalStatus method returns the approval status
     *
     */
    public function test_get_approval_status() {
        $this->assertEquals(ApprovalStatus::unapproved(), $this->cat->getApprovalStatus());
    }

    /** test_get_children
     *
     *  Ensures the getChildren method returns the children collection
     *
     */
    public function test_get_children() {
        $this->assertTrue($this->cat->getChildren() instanceof ArrayCollection);
    }

    /** test_add_child
     *
     *  Ensures the addChild method returns a new category
     *
     */
    public function test_add_child() {
        $this->assertTrue($this->cat->addChild("Test Name") instanceof Category);
    }

}