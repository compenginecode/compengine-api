<?php

namespace DomainLayer\ORM\FeatureVectorIndex;

use Doctrine\Common\Collections\ArrayCollection;
use DomainLayer\Common\DomainEntity\DomainEntityAsTrait;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\ORM\FeatureVectorIndex\HashTableCollection\HashTableCollection;
use DomainLayer\ORM\LSHOptions\LSHOptions;

/**
 * Class FeatureVectorIndex
 * @package DomainLayer\ORM\FeatureVectorIndex
 */
class FeatureVectorIndex {

	use DomainEntityAsTrait;

	protected $family;

	protected $lshOptions;

	/**
	 * @var array
	 */
	protected $hashTables;

	public function __construct(FeatureVectorFamily $featureVectorFamily, LSHOptions $lshOptions){
		$this->family = $featureVectorFamily;
		$this->lshOptions = $lshOptions;

		$this->hashTables = new ArrayCollection();
	}

	/**
	 * @return LSHOptions
	 */
	public function getLshOptions() {
		return $this->lshOptions;
	}

	/**
	 * @return HashTableCollection
	 */
	public function hashTables() {
		return new HashTableCollection($this->hashTables, $this);
	}

}