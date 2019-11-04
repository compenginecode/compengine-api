<?php

namespace DomainLayer\ORM\FeatureVectorFamily\DescriptorCollection;

use DomainLayer\Common\Collection\Collection;
use DomainLayer\ORM\FeatureVectorDescriptor\FeatureVectorDescriptor;
use DomainLayer\ORM\FeatureVectorFamily\DescriptorCollection\Exceptions\EAdditionalDescriptorNotAllowed;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;

/**
 * Class DescriptorCollection
 * @package DomainLayer\ORM\FeatureVectorFamily\DescriptorCollection
 */
class DescriptorCollection extends Collection{

	/** getParentFamily
	 *
	 * 	Returns the parent feature vector family/
	 *
	 * @return FeatureVectorFamily
	 */
	public function getParentFamily(){
		return $this->owner;
	}

	/** addDescriptor
	 *
	 * 	Adds and returns a new descriptor.
	 *
	 * @param $name
	 * @param $mappingId
	 * @return FeatureVectorDescriptor
	 * @throws EAdditionalDescriptorNotAllowed
	 */
	public function addDescriptor($name, $mappingId){
		/** HashTables depend on the descriptor count, so cannot change post-addition. */
		if ($this->getParentFamily()->totalHashTables() > 0){
			throw new EAdditionalDescriptorNotAllowed();
		}

		$descriptor = new FeatureVectorDescriptor($name, $mappingId, $this->getParentFamily());
		$this->arrayCollection->add($descriptor);

		return $descriptor;
	}

	public function getByMappingId($mappingId){
		$result = NULL;
		foreach($this->arrayCollection as $aFeatureVectorDescriptor){
			/** @var $aFeatureVectorDescriptor FeatureVectorDescriptor */
			if ($aFeatureVectorDescriptor->getMappingId() === $mappingId){
				$result = $aFeatureVectorDescriptor;
				break;
			}
		}

		return $result;
	}

}