<?php

namespace UnitTests\DomainLayer\ORM\TimeSeries;

use Doctrine\Common\Collections\ArrayCollection;
use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\Contributor\Contributor;
use DomainLayer\ORM\SamplingInformation\SamplingInformation;
use DomainLayer\ORM\Source\Source;
use DomainLayer\ORM\Tag\Tag;
use DomainLayer\ORM\TimeSeries\TimeSeries;
use Mockery\Mock;

/**
 * Class TimeSeriesTest
 * @package UnitTests\DomainLayer\ORM\TimeSeries
 */
class TimeSeriesTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var TimeSeries
     */
    private $series;
    /**
     * @var \ReflectionObject
     */
    private $reflection;

    /** setUp
     *
     *  Set up the test class
     *
     */
    public function setUp(){
        $name = "Test Name";
        $desc = "Test Description";
        $src = new Source("Test Source");
        $catMock = \Mockery::mock(Category::class);
        $rate = "Test Rate";
        $unit = "Test Unit";
        $info = new SamplingInformation(SamplingInformation::SAMPLING_DEFINED, $rate, $unit);
        $tags = [
            new Tag("Tag 1"),
            new Tag("Tag 2"),
            new Tag("Tag 3")
        ];
        $points = [1,2,3,4,5,6,7,8,9,10,11,12];

        $this->series = new TimeSeries($name, $desc, $src, $catMock, $info, $tags, $points);
        $this->reflection = new \ReflectionObject($this->series);
    }

    /** tearDown
     *
     *  Closes and tests all Mockery assertions.
     *
     */
    public function tearDown(){
        \Mockery::close();
    }

    /** test_set_data_points
     *
     *  Ensures the setDataPoints method sets the data points
     *
     */
    public function test_set_data_points() {
        $reflectionMethod = $this->reflection->getMethod('setDataPoints');
        $reflectionMethod->setAccessible(TRUE);
        $points = [2,4,6,8,10,12,14,16,18,20];
        $reflectionMethod->invokeArgs($this->series, [$points]);

        $this->assertEquals($points, $this->series->getDataPoints());
    }

    /** test_set_description
     *
     *  Ensures the setDescription method sets the description if a valid string is passed in
     *
     */
    public function test_set_description() {
        $reflectionMethod = $this->reflection->getMethod('setDescription');
        $reflectionMethod->setAccessible(TRUE);
        $desc = "Something something";
        $reflectionMethod->invokeArgs($this->series, [$desc]);

        $this->assertEquals($desc, $this->series->getDescription());
    }

    /** test_set_description_not_string
     *
     *  Ensures the setDescription method sets the description to "" if argument is not valid string
     *
     */
    public function test_set_description_not_string() {
        $reflectionMethod = $this->reflection->getMethod('setDescription');
        $reflectionMethod->setAccessible(TRUE);
        $desc = [1,2,3];
        $reflectionMethod->invokeArgs($this->series, [$desc]);

        $this->assertEquals("", $this->series->getDescription());
    }

    /** test_set_slug
     *
     *  Ensures the setSlug method sets the slug
     *
     */
    public function test_set_slug() {
        $reflectionMethod = $this->reflection->getMethod('setSlug');
        $reflectionMethod->setAccessible(TRUE);
        $slug = "Test Slug";
        $reflectionMethod->invokeArgs($this->series, [$slug]);

        $this->assertEquals($slug, $this->series->getSlug());
    }


    /** test_set_tags
     *
     *  Ensures the setTags method sets the tags
     *
     */
    public function test_set_tags() {
        $reflectionMethod = $this->reflection->getMethod('setTags');
        $reflectionMethod->setAccessible(TRUE);
        $tags = [
            new Tag("New Tag 1"),
            new Tag("New Tag 2")
        ];
        $reflectionMethod->invokeArgs($this->series, [$tags]);

        $this->assertEquals($tags, $this->series->getTags());
    }

    /** test_set_category
     *
     *  Ensures the setCategory method sets the category
     *
     */
    public function test_set_category() {
        $reflectionMethod = $this->reflection->getMethod('setCategory');
        $reflectionMethod->setAccessible(TRUE);
        $catMock = \Mockery::mock(Category::class);
        $reflectionMethod->invokeArgs($this->series, [$catMock]);

        $this->assertEquals($catMock, $this->series->getCategory());
    }

    /** test_set_sampling_information
     *
     *  Ensures the setSamplingInformation method sets the sampling information
     *
     */
    public function test_set_sampling_information() {
        $reflectionMethod = $this->reflection->getMethod('setSamplingInformation');
        $reflectionMethod->setAccessible(TRUE);
        $info = new SamplingInformation(SamplingInformation::SAMPLING_DEFINED, "New Rate", "New Unit");
        $reflectionMethod->invokeArgs($this->series, [$info]);

        $this->assertEquals($info, $this->series->getSamplingInformation());
    }

    /** test_constructor
     *
     *  Ensures the object is constructed correctly
     *
     */
    public function test_constructor() {
        $name = "Test Name";
        $desc = "Test Description";
        $src = new Source("Test Source");
        $catMock = \Mockery::mock(Category::class);
        $rate = "Test Rate";
        $unit = "Test Unit";
        $info = new SamplingInformation(SamplingInformation::SAMPLING_DEFINED, $rate, $unit);
        $tags = [
            new Tag("Tag 1"),
            new Tag("Tag 2"),
            new Tag("Tag 3")
        ];
        $points = [1,2,3,4,5,6,7,8,9,10,11,12];
        $tagsCollection = new ArrayCollection();
        $tagsCollection->add(new Tag("Tag 1"));
        $tagsCollection->add(new Tag("Tag 2"));
        $tagsCollection->add(new Tag("Tag 3"));

        $series = new TimeSeries($name, $desc, $src, $catMock, $info, $tags, $points);

        $this->assertEquals($name, $series->getName());
        $this->assertEquals($desc, $series->getDescription());
        $this->assertEquals($src, $series->getSource());
        $this->assertEquals($catMock, $series->getCategory());
        $this->assertEquals($info, $series->getSamplingInformation());
        $this->assertEquals($tagsCollection, $series->getTags());
        $this->assertEquals($points, $series->getDataPoints());
        $this->assertTrue($series instanceof TimeSeries);
    }

    /** test_set_contributor
     *
     *  Ensures the setContributor method sets the contributor
     *
     */
    public function test_set_contributor() {
        $reflectionMethod = $this->reflection->getMethod('setContributor');
        $reflectionMethod->setAccessible(TRUE);
        $contributor = new Contributor("Test Name", "Test Email");
        $reflectionMethod->invokeArgs($this->series, [$contributor]);

        $this->assertEquals($contributor, $this->series->getContributor());
    }

    /** test_get_contributor
     *
     *  Ensures the getContributor method gets the contributor
     *
     */
    public function test_get_contributor() {
        $reflectionMethod = $this->reflection->getMethod('setContributor');
        $reflectionMethod->setAccessible(TRUE);
        $contributor = new Contributor("Test Name", "Test Email");
        $reflectionMethod->invokeArgs($this->series, [$contributor]);

        $this->assertEquals($contributor, $this->series->getContributor());
    }

    /** test_get_data_points
     *
     *  Ensures the getDataPoints method gets the data points
     *
     */
    public function test_get_data_points() {
        $points = [1,2,3,4,5,6,7,8,9,10,11,12];

        $this->assertEquals($points, $this->series->getDataPoints());
    }

    /** test_get_name
     *
     *  Ensures the getName method returns the name
     *
     */
    public function test_get_name() {
        $name = "Test Name";

        $this->assertEquals($name, $this->series->getName());
    }

    /** test_get_slug
     *
     *  Ensures the getSlug method gets the slug
     *
     */
    public function test_get_slug() {
        $reflectionMethod = $this->reflection->getMethod('setSlug');
        $reflectionMethod->setAccessible(TRUE);
        $slug = "Test Slug";
        $reflectionMethod->invokeArgs($this->series, [$slug]);

        $this->assertEquals($slug, $this->series->getSlug());
    }

    /** test_get_description
     *
     *  Ensures the getDescription method returns the description
     *
     */
    public function test_get_description() {
        $desc = "Test Description";

        $this->assertEquals($desc, $this->series->getDescription());
    }

    /** test_get_source
     *
     *  Ensures the getSource method returns the source
     *
     */
    public function test_get_source() {
        $src = new Source("Test Source");

        $this->assertEquals($src, $this->series->getSource());
    }

    /** test_get_tags
     *
     *  Ensures the getTags method returns the tags
     *
     */
    public function test_get_tags() {
        $tagsCollection = new ArrayCollection();
        $tagsCollection->add(new Tag("Tag 1"));
        $tagsCollection->add(new Tag("Tag 2"));
        $tagsCollection->add(new Tag("Tag 3"));

        $this->assertEquals($tagsCollection, $this->series->getTags());
    }

    /** test_get_category
     *
     *  Ensures the getCategory method returns the category
     *
     */
    public function test_get_category() {
        $catMock = \Mockery::mock(Category::class);

        $this->assertEquals($catMock, $this->series->getCategory());
    }

    /** test_get_sampling_information
     *
     *  Ensures the getSamplingInformation method returns the sampling information
     *
     */
    public function test_get_sampling_information() {
        $rate = "Test Rate";
        $unit = "Test Unit";
        $info = new SamplingInformation(SamplingInformation::SAMPLING_DEFINED, $rate, $unit);

        $this->assertEquals($info, $this->series->getSamplingInformation());
    }

}