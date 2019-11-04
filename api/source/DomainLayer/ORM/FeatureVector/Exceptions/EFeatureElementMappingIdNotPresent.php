<?php

namespace DomainLayer\ORM\FeatureVector\Exceptions;

/**
 * Class EFeatureElementMappingIdNotPresent
 * @package DomainLayer\ORM\FeatureVector\Exceptions
 */
class EFeatureElementMappingIdNotPresent extends \Exception{

	/** __construct
	 *
	 * 	EFeatureElementMappingIdNotPresent constructor.
	 *
	 * @param string $mappingId
	 */
	public function __construct($mappingId){
		parent::__construct("The feature element MappingId '$mappingId' is not contained within this feature vector.");
	}

}