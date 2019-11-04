<?php

namespace DomainLayer\TimeSeriesManagement\Comparison\ComparisonStatusService\Exceptions;

/**
 * Class EComparisonKeyNotFound
 * @package DomainLayer\TimeSeriesManagement\Comparison\ComparisonStatusService\Exceptions
 */
class EComparisonKeyNotFound extends \Exception{

	/** __construct
	 *
	 * 	EComparisonKeyNotFound constructor.
	 *
	 * @param string $comparisonKey
	 */
	public function __construct($comparisonKey){
		parent::__construct("The comparison key '$comparisonKey' was not found in the cache. It is either invalid or has expired.");
	}

}