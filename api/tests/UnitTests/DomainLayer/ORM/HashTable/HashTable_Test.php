<?php

namespace Tests\UnitTests\DomainLayer\ORM\HashTable;

use Doctrine\Common\Collections\ArrayCollection;
use DomainLayer\Common\Vector\Vector;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\ORM\HashTable\HashTable;

/**
 * Class HashTable_Test
 * @package Tests\UnitTests\DomainLayer\ORM\HashTable
 */
class HashTable_Test extends \PHPUnit_Framework_TestCase {

	/** test_can_instantiate_class
	 *
	 * 	Ensures that the class can be instantiated.
	 *
	 */
	public function test_can_instantiate_class(){
		/** @var FeatureVectorFamily $mockFeatureVectorFamily */
		$mockFeatureVectorFamily = \Mockery::mock("DomainLayer\\ORM\\FeatureVectorFamily\\FeatureVectorFamily")
			->shouldReceive([
				"descriptors" => \Mockery::mock(ArrayCollection::class)
					->shouldReceive("count")
					->andReturn(20)
					->getMock(),
				"getLshHashCount" => 10
			])
			->getMock();

		$instance = new HashTable($mockFeatureVectorFamily, 1);
		$this->assertNotNull($instance);
	}

	/** test_can_generate_hashTables_correctly
	 *
	 * 	Ensures that the hash generated has the correct length.
	 *
	 */
	public function test_can_generate_hashes_of_correct_length(){
		$hashTableCount = 5;

		/** @var FeatureVectorFamily $mockFeatureVectorFamily */
		$mockFeatureVectorFamily = \Mockery::mock("DomainLayer\\ORM\\FeatureVectorFamily\\FeatureVectorFamily")
			->shouldReceive([
				"descriptors" => \Mockery::mock(ArrayCollection::class)
					->shouldReceive("count")
					->andReturn(4)
					->getMock(),
				"getLshHashCount" => $hashTableCount
			])
			->getMock();

		$instance = new HashTable($mockFeatureVectorFamily, 1);
		$hash = $instance->hash(Vector::fromArray([1, 2, 3, 4]));

		$this->assertEquals($hashTableCount, strlen($hash), "The hash generated has the correct length");
	}

}