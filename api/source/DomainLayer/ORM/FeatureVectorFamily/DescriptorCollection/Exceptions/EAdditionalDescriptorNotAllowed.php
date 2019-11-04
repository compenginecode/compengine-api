<?php

namespace DomainLayer\ORM\FeatureVectorFamily\DescriptorCollection\Exceptions;

/**
 * Class EAdditionalDescriptorNotAllowed
 * @package DomainLayer\ORM\FeatureVectorFamily\DescriptorCollection\Exceptions
 */
class EAdditionalDescriptorNotAllowed extends \Exception{

	/** __construct
	 *
	 * 	EAdditionalDescriptorNotAllowed constructor.
	 *
	 */
	public function __construct(){
		parent::__construct("You cannot add descriptors once hyperplanes are added.");
	}

}