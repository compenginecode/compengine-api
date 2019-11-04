<?php

namespace DomainLayer\ORM\FeatureVectorFamily;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DomainLayer\ORM\DomainEntity\DomainEntity;
use DomainLayer\ORM\FeatureVectorFamily\DescriptorCollection\DescriptorCollection;
use DomainLayer\ORM\FeatureVectorIndex\FeatureVectorIndex;
use DomainLayer\ORM\FeatureVectorIndex\HashTableCollection\HashTableCollection;
use DomainLayer\ORM\LSHOptions\LSHOptions;

/**
 * Class FeatureVectorFamily
 * @package DomainLayer\ORM\FeatureVectorFamily
 */
class FeatureVectorFamily extends DomainEntity{

	/** $name
	 *
	 * 	A name for this feature vector family.
	 *
	 * @var string
	 */
	protected $name;

	/** $description
	 *
	 * 	A basic text description.
	 *
	 * @var string
	 */
	protected $description;

	/** $generatorScriptPath
	 *
	 * 	A fully qualified path to the generator script to use.
	 *
	 * @var string
	 */
	protected $generatorScriptPath;

	/**
	 * @var Collection
	 */
	protected $descriptors;

	protected $commonIndex;

	protected $syntheticIndex;

	protected $realIndex;

	protected $indexName;

	/**
	 * FeatureVectorFamily constructor.
	 * @param $name
	 * @param $description
	 * @param $generatorScriptPath
	 * @param LSHOptions $commonIndexLSHOptions
	 * @param LSHOptions $syntheticIndexLSHOptions
	 * @param LSHOptions $realIndexLSHOptions
	 */
	public function __construct($name, $description, $generatorScriptPath, LSHOptions $commonIndexLSHOptions,
		LSHOptions $syntheticIndexLSHOptions, LSHOptions $realIndexLSHOptions) {

		$this->name = $name;
		$this->description = $description;
		$this->generatorScriptPath = $generatorScriptPath;
		$this->descriptors = new ArrayCollection();

		$this->indexName = md5(rand());
		$this->commonIndex = new FeatureVectorIndex($this, $commonIndexLSHOptions);
		$this->syntheticIndex = new FeatureVectorIndex($this, $syntheticIndexLSHOptions);
		$this->realIndex = new FeatureVectorIndex($this, $realIndexLSHOptions);
	}

	/**
	 * @return DescriptorCollection
	 */
	public function descriptors(){
		return new DescriptorCollection($this->descriptors, $this);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getIndexName() {
		return $this->indexName;
	}



	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getGeneratorScriptPath() {
		return $this->generatorScriptPath;
	}

	/**
	 * @return FeatureVectorIndex
	 */
	public function getCommonIndex() {
		return $this->commonIndex;
	}

	/**
	 * @return FeatureVectorIndex
	 */
	public function getSyntheticIndex() {
		return $this->syntheticIndex;
	}

	/**
	 * @return FeatureVectorIndex
	 */
	public function getRealIndex() {
		return $this->realIndex;
	}

	public function prepareIndices(){
		$this->commonIndex->hashTables()->generateHashTables($this->descriptors()->count());
		$this->syntheticIndex->hashTables()->generateHashTables($this->descriptors()->count());
		$this->realIndex->hashTables()->generateHashTables($this->descriptors()->count());
	}

	public function totalHashTables(){
		return $this->commonIndex->hashTables()->count()
			+ $this->syntheticIndex->hashTables()->count()
			+ $this->realIndex->hashTables()->count();
	}

}