<?php

namespace DomainLayer\ORM\FeatureVector;
use DomainLayer\Common\Vector\Vector;
use DomainLayer\ORM\FeatureVector\Exceptions\EFeatureElementMappingIdNotPresent;

/**
 * Class FeatureVector
 * @package DomainLayer\ORM\FeatureVector
 */
class FeatureVector {

	protected $featureElementValues = [];

	public function __construct(array $featureElementValues = []){
		$this->featureElementValues = $featureElementValues;
	}

	public static function fromArray(array $array){
		return new self($array);
	}

	/** addElementValue
	 *
	 * 	Adds a feature element value assigned to the given feature element MappingId.
	 *
	 * @param $featureElementMappingId
	 * @param $featureElementValue
	 */
	public function addElementValue($featureElementMappingId, $featureElementValue){
		$this->featureElementValues[$featureElementMappingId] = (float)$featureElementValue;
	}

	/** getElementValue
	 *
	 * 	Returns the feature element value given the feature element MappingId.
	 *
	 * @param $featureElementMappingId
	 * @return float
	 * @throws EFeatureElementMappingIdNotPresent
	 */
	public function getElementValue($featureElementMappingId){
		if (isset($this->featureElementValues[$featureElementMappingId])){
			return $this->featureElementValues[$featureElementMappingId];
		}

		throw new EFeatureElementMappingIdNotPresent($featureElementMappingId);
	}

	/** toArray
	 *
	 * 	Converts this class into an array.
	 *
	 * @return string
	 */
	public function toArray(){
		return $this->featureElementValues;
	}

	/**
	 * @return Vector
	 */
	public function toVector(){
		return new Vector($this->featureElementValues);
	}

	public function toHash(){
		return implode("", $this->featureElementValues);
	}
	
}