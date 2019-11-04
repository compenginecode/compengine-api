<?php

namespace DomainLayer\ORM\FeatureVectorIndex\HashTableCollection;

use DomainLayer\Common\Collection\Collection;
use DomainLayer\Common\Vector\Vector;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\ORM\FeatureVectorIndex\FeatureVectorIndex;
use DomainLayer\ORM\FeatureVectorIndex\HashTableCollection\Exceptions\EPersistBeforeGenerateHash;
use DomainLayer\ORM\HashTable\HashTable;

/**
 * Class HashTableCollection
 * @package DomainLayer\ORM\FeatureVectorIndex\HashTableCollection
 */
class HashTableCollection extends Collection{

	/** getParentFamily
	 *
	 * 	Returns the parent feature vector family.
	 *
	 * @return FeatureVectorIndex
	 */
	public function getParentHashTable(){
		return $this->owner;
	}

	/** generateHashTables
	 *
	 * 	Adds the required number of hash tables into the feature vector
	 * 	family.
	 *
	 */
	public function generateHashTables($numberOfDescriptors){
		for($i = 0; $i < $this->getParentHashTable()->getLshOptions()->getIndexCount(); $i++){
			$hashTable = new HashTable($this->getParentHashTable(), $i, $numberOfDescriptors);
			$this->arrayCollection->add($hashTable);
		}
	}

	public function generateHash(Vector $vector){
		$indexHashes = [];
		foreach($this->arrayCollection as $aHashTable){
			/** @var $aHashTable HashTable */
			if (NULL === $aHashTable->getId()){
				throw new EPersistBeforeGenerateHash();
			}

			$indexHashes[$aHashTable->getId()] = $aHashTable->hash($vector);
		}

		return $indexHashes;
	}

}