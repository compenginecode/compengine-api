<?php

namespace DomainLayer\ORM\HashTable;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DomainLayer\Common\Vector\Vector;
use DomainLayer\ORM\DomainEntity\DomainEntity;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\ORM\FeatureVectorIndex\FeatureVectorIndex;
use DomainLayer\ORM\HashTable\HyperplaneCollection\HyperplaneCollection;
use DomainLayer\ORM\Hyperplane\Hyperplane;

/**
 * Class HashTable
 * @package DomainLayer\ORM\HashTable
 */
class HashTable extends DomainEntity{

	/** $family
	 *
	 * 	Reference to the feature vector index that this hash table
	 * 	belongs to.
	 *
	 * @var FeatureVectorIndex
	 */
	protected $index;

	/** $hyperplanes
	 *
	 * 	Collection of hyperplanes within this hash table.
	 *
	 * @var Collection
	 */
	protected $hyperplanes;

	/** $indexNumber
	 *
	 * 	The number of this index.
	 *
	 * @var integer
	 */
	protected $indexNumber;

	/** generateHyperplanes
	 *
	 * 	Generates all the hyperplanes for the given feature vector family.
	 *
	 */
	protected function generateHyperplanes($numberOfDescriptors){
		for($i = 0; $i < $this->index->getLshOptions()->getHashCount(); $i++){
			$this->hyperplanes()->addHyperplane($numberOfDescriptors);
		}
	}

	/** __construct
	 *
	 * 	HashTable constructor.
	 *
	 * @param FeatureVectorIndex $featureVectorIndex
	 * @param $indexNumber
	 */
	public function __construct(FeatureVectorIndex $featureVectorIndex, $indexNumber, $numberOfDescriptors){
		$this->index = $featureVectorIndex;
		$this->indexNumber = $indexNumber;
		$this->hyperplanes = new ArrayCollection();

		/** Generate all the hyperplanes */
		$this->generateHyperplanes($numberOfDescriptors);
	}

	/**
	 * @return HyperplaneCollection
	 */
	public function hyperplanes() {
		return new HyperplaneCollection($this->hyperplanes, $this);
	}

	/** hash
	 *
	 * 	Hashes the given vector against the hyperplanes present. Returns a bitstring
	 *	of zeros and ones.
	 *
	 * @param Vector $vector
	 * @return string
	 */
	public function hash(Vector $vector){
		$bitString = "";
		foreach($this->hyperplanes() as $aHyperplane){
			/** @var $aHyperplane Hyperplane */
			$bitString .= (string)$aHyperplane->hash($vector);
		}

		return $bitString;
	}

	/**
	 * @return FeatureVectorIndex
	 */
	public function getFeatureVectorIndex() {
		return $this->index;
	}

	/**
	 * @return int
	 */
	public function getIndexNumber() {
		return $this->indexNumber;
	}

}