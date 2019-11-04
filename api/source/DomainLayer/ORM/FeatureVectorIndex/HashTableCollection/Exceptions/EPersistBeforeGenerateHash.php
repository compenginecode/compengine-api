<?php

namespace DomainLayer\ORM\FeatureVectorIndex\HashTableCollection\Exceptions;

/**
 * Class EPersistBeforeGenerateHash
 * @package DomainLayer\ORM\FeatureVectorIndex\HashTableCollection\Exceptions
 */
class EPersistBeforeGenerateHash extends \Exception{

	/** __construct
	 *
	 * 	EPersistBeforeGenerateHash constructor.
	 *
	 */
	public function __construct(){
		parent::__construct("All hash tables must be persisted and have a unique ID before a hash family can be generated.");
	}

}