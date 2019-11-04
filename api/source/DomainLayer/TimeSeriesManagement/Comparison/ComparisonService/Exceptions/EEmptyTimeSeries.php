<?php

namespace DomainLayer\TimeSeriesManagement\Comparison\ComparisonService\Exceptions;

/**
 * Class EEmptyTimeSeries
 * @package DomainLayer\TimeSeriesManagement\Comparison\ComparisonService\Exceptions
 */
class EEmptyTimeSeries extends \Exception{

	/** __construct
	 *
	 * 	EEmptyTimeSeries constructor.
	 *
	 */
	public function __construct(){
		parent::__construct("The time series is empty.");
	}

}