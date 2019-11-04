<?php

namespace DomainLayer\ORM\FeatureVectorDescriptor;

use DomainLayer\ORM\DomainEntity\DomainEntity;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;

/**
 * Class FeatureVectorDescriptor
 * @package DomainLayer\ORM\FeatureVectorDescriptor
 */
class FeatureVectorDescriptor extends DomainEntity{

	/** $name
	 *
	 * 	The name of this feature vector.
	 *
	 * @var string
	 */
	protected $name;

	/** $prettyName
	 *
	 * 	The name of this feature vector.
	 *
	 * @var string
	 */
	protected $prettyName;

	/** $mappingId
	 *
	 * 	The mapping ID of this feature vector. This is a user-defined ID that
	 * 	assist with mapping raw CLI output from the feature vector generator
	 * 	to a descriptor.
	 *
	 * @var string
	 */
	protected $mappingId;

	/** $family
	 *
	 * 	The parent feature vector family to which this descriptor belongs.
	 *
	 * @var FeatureVectorFamily
	 */
	protected $family;

	/**
	 * FeatureVectorDescriptor constructor.
	 * @param $name
	 * @param $mappingId
	 * @param FeatureVectorFamily $family
	 */
	public function __construct($name, $mappingId, FeatureVectorFamily $family) {
		$this->name = $name;
		$this->prettyName = $name;
		$this->mappingId = $mappingId;
		$this->family = $family;
	}

	/**
	 * @return string
	 */
	public function getPrettyName()
	{
		return $this->prettyName;
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
	public function getMappingId() {
		return $this->mappingId;
	}

}