<?php

namespace UnitTests\DomainLayer\ORM\SiteAttribute;

use DomainLayer\ORM\SiteAttribute\SiteAttribute;

/**
 * Class SiteAttributeTest
 * @package UnitTests\DomainLayer\ORM\SiteAttribute
 */
class SiteAttributeTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var SiteAttribute
     */
    private $attr;

    /** setUp
     *
     *  Set up the test class
     *
     */
    public function setUp(){
        $key = "Test Key";
        $value = "Test Value";
        $this->attr = new SiteAttribute($key, $value);
    }

    /** test_constructor
     *
     *  Ensures the object is constructed correctly
     *
     */
    public function test_constructor() {
        $key = "Test Key";
        $value = "Test Value";
        $attr = new SiteAttribute($key, $value);

        $this->assertEquals($key, $attr->getKey());
        $this->assertEquals($value, $attr->getValue());
        $this->assertTrue($attr instanceof SiteAttribute);
    }

    /** test_get_key
     *
     *  Ensures the getKey method returns the key
     *
     */
    public function test_get_key() {
        $key = "Test Key";

        $this->assertEquals($key, $this->attr->getKey());
    }

    /** test_get_value
     *
     *  Ensures the getValue method returns the value
     *
     */
    public function test_get_value() {
        $value = "Test Value";

        $this->assertEquals($value, $this->attr->getValue());
    }

}