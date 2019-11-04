<?php

namespace Tests\UnitTests\DomainLayer\ORM\Hyperplane;

use DomainLayer\Common\Vector\Vector;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\ORM\HashTable\HashTable;
use DomainLayer\ORM\Hyperplane\Hyperplane;
use Mockery\Mock;

/**
 * Class Hyperplane_Test
 * @package Tests\UnitTests\DomainLayer\ORM\Hyperplane
 */
class Hyperplane_Test extends \PHPUnit_Framework_TestCase {

	/** test_can_instantiate_class
	 *
	 * 	Ensures that the class can be instantiated.
	 *
	 */
	public function test_can_instantiate_class(){
		/** @var HashTable $mockHashTable */
		$mockHashTable = \Mockery::mock("DomainLayer\\ORM\\HashTable\\HashTable");
		$instance = new Hyperplane($mockHashTable, 1);
		$this->assertNotNull($instance);
	}

	/** test_partition_vector_is_generated_on_construction
	 *
	 * 	Ensures that when the hyperplane is constructed, the partition vector is
	 * 	initialized and has the correct dimension.
	 *
	 */
	public function test_partition_vector_is_generated_on_construction(){
		/** @var HashTable $mockHashTable */
		$dimension = 5;
		$mockHashTable = \Mockery::mock("DomainLayer\\ORM\\HashTable\\HashTable");
		$instance = new Hyperplane($mockHashTable, $dimension);

		$this->assertEquals($dimension, $instance->getPartitionVector()->dimension(),
			"Partition vector has correct dimension and is created on construction.");
	}

	/** test_partition_vector_is_gaussian
	 *
	 * 	Ensures that when a partition vector is generated, the elements of that vector
	 * 	are indeed numbers and appear Gaussian.
	 *
	 */
	public function test_partition_vector_is_gaussian(){
		/** @var HashTable $mockHashTable */
		$dimension = 5;
		$mockHashTable = \Mockery::mock("DomainLayer\\ORM\\HashTable\\HashTable");
		$instance = new Hyperplane($mockHashTable, $dimension);

		foreach($instance->getPartitionVector() as $anElement){
			$this->assertTrue(is_numeric($anElement), "Partition vector has correctly formatted elements.");
		}

		/** Todo: We need to somehow test for Gaussian element values */
	}

	/** test_partition_vector_hashes_positives_correctly
	 *
	 * 	Ensures that when a partition vector is hashed against a candidate vector,
	 *  the correct sign is returned for the positive case.
	 *
	 */
	public function test_partition_vector_hashes_positives_correctly(){
		/** @var Hyperplane $hyperplane */
		$hyperplane = \Mockery::mock(Hyperplane::class)
			->shouldReceive("getPartitionVector")
			->andReturn(new Vector([1, 1]))
			->getMock()
			->makePartial();

		$candidateVector = new Vector([1, 1]);

		$expectedSign = 1;
		$sign = $hyperplane->hash($candidateVector);
		$this->assertEquals($expectedSign, $sign, "Hyperplane hashes correctly for positive-sign results");
	}

	/** test_partition_vector_hashes_negatives_correctly
	 *
	 * 	Ensures that when a partition vector is hashed against a candidate vector,
	 *  the correct sign is returned for the negative case.
	 *
	 */
	public function test_partition_vector_hashes_negatives_correctly(){
		/** @var Hyperplane $hyperplane */
		$hyperplane = \Mockery::mock(Hyperplane::class)
			->shouldReceive("getPartitionVector")
			->andReturn(new Vector([1, 1]))
			->getMock()
			->makePartial();

		$candidateVector = new Vector([-1, -1]);

		$expectedSign = 0;
		$sign = $hyperplane->hash($candidateVector);
		$this->assertEquals($expectedSign, $sign, "Hyperplane hashes correctly for negative-sign results");
	}

}