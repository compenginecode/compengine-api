<?php

namespace InfrastructureLayer\ElasticSearch\Exceptions;

/**
 * Class EFeatureVectorDocumentMismatch
 * @package InfrastructureLayer\ElasticSearch\Exceptions
 */
class EFeatureVectorDocumentMismatch extends \Exception{

	/** __construct
	 *
	 * 	EFeatureVectorDocumentMismatch constructor.
	 *
	 * @param string $message
	 */
	public function __construct($message){
		parent::__construct("Feature vector document mismatch: $message");
	}

}