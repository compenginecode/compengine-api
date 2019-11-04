<?php

namespace Tests\UnitTests\DomainLayer\ORM\HashTableCollection;

use Doctrine\Common\Collections\ArrayCollection;
use DomainLayer\Common\Vector\Vector;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\ORM\FeatureVectorIndex\HashTableCollection\HashTableCollection;
use DomainLayer\ORM\HashTable\HashTable;

/**
 * Class HashTableCollection_Test
 * @package Tests\UnitTests\DomainLayer\ORM\HashTableCollection
 */
class HashTableCollection_Test extends \PHPUnit_Framework_TestCase {

	/** test_can_instantiate_class
	 *
	 * 	Ensures that the class can be instantiated.
	 *
	 */
	public function test_can_instantiate_class(){
		$fvf = \Mockery::mock("DomainLayer\\ORM\\FeatureVectorFamily\\FeatureVectorFamily");
		$instance = new HashTableCollection(new ArrayCollection(), $fvf);
		$this->assertNotNull($instance);
	}

	/** test_can_generate_hash_tables_correctly
	 *
	 * 	Ensures that the correct number of hash tables are generated.
	 *
	 */
	public function test_can_generate_hash_tables_correctly(){
		$hashTableCount = 5;

		/** @var FeatureVectorFamily $fvf */
		$fvf = \Mockery::mock("DomainLayer\\ORM\\FeatureVectorFamily\\FeatureVectorFamily")
			->shouldReceive([
				"descriptors" => \Mockery::mock(ArrayCollection::class)
					->shouldReceive("count")
					->andReturn(20)
					->getMock(),
				"getLshIndexCount" => $hashTableCount,
				"getLshHashCount" => 5
			])
			->getMock();

		$instance = new HashTableCollection(new ArrayCollection(), $fvf);
		$instance->generateHashTables();

		$this->assertEquals($hashTableCount, $instance->count(), "The correct number of hash tables are generated");
	}

	/** test_generation_of_a_hash_family_with_no_saved_has_tables_throws_exception
	 *
	 * 	Ensures that when a hash family is requested, an exception is thrown if any of the hash tables
	 *	are not yet persisted.
	 *
	 * @expectedException \DomainLayer\ORM\FeatureVectorIndex\HashTableCollection\Exceptions\EPersistBeforeGenerateHash
	 */
	public function test_generation_of_a_hash_family_with_no_saved_has_tables_throws_exception(){
		$lshIndexCount = 2;
		$lshHashCount = 2;

		$featureVectorFamily = new FeatureVectorFamily("LSH", "LSH", 1, "Empty", $lshIndexCount, $lshHashCount);
		$featureVectorFamily->descriptors()->addDescriptor("Feature Element A", "fe1");
		$featureVectorFamily->descriptors()->addDescriptor("Feature Element B", "fe2");

		$featureVectorFamily->hashTables()->generateHashTables();
		/** Note that we have no saved hash tables - so no hash table IDs! */
		$featureVectorFamily->hashTables()->generateHash(Vector::fromArray([1, 2]));
	}

	/** test_generation_of_a_hash_family_works_as_required
	 *
	 * 	Ensures that when a hash family is requested, the returned array is of the correct length
	 * 	and that each hash is of the correct length.
	 *
	 */
	public function test_generation_of_a_hash_family_works_as_required(){
		$lshIndexCount = 2;
		$lshHashCount = 2;

		$featureVectorFamily = new FeatureVectorFamily("LSH", "LSH", 1, "Empty", $lshIndexCount, $lshHashCount);
		$featureVectorFamily->descriptors()->addDescriptor("Feature Element A", "fe1");
		$featureVectorFamily->descriptors()->addDescriptor("Feature Element B", "fe2");

		$featureVectorFamily->hashTables()->generateHashTables();
		foreach($featureVectorFamily->hashTables() as $aHashTable){
			/** @var $aHashTable HashTable */
			$reflectedProperty = new \ReflectionProperty(get_class($aHashTable), "id");
			$reflectedProperty->setAccessible(TRUE);
			$reflectedProperty->setValue($aHashTable, $aHashTable->getIndexNumber());
		}

		$hashFamily = $featureVectorFamily->hashTables()->generateHash(Vector::fromArray([1, 2]));

		$this->assertEquals($lshIndexCount, count($hashFamily));
		foreach($hashFamily as $aHashFamilyId => $aHashString){
			$this->assertEquals($lshHashCount, strlen($aHashString));
		}
	}

}