<?php

namespace DomainLayer\TimeSeriesManagement\Comparison\UploadService\Exceptions;

/**
 * Class EComparisonKeyMissing
 * @package DomainLayer\TimeSeriesManagement\Comparison\UploadService\Exceptions
 */
class EComparisonKeyMissing extends \Exception{

	/** __construct
	 *
	 * 	EComparisonKeyMissing constructor.
	 *
	 * @param string $comparisonKey
	 */
	public function __construct($comparisonKey){
		parent::__construct("The comparison key '$comparisonKey' was not found.'");
	}

}