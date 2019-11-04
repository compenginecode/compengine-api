<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\Converters\SoxConverter\Exceptions;

/**
 * Class ESoxError
 * @package DomainLayer\TimeSeriesManagement\Ingestion\Converters\SoxConverter\Exceptions
 */
class ESoxError extends \Exception{

	/** __construct
	 *
	 * 	ESoxError constructor.
	 *
	 * @param \Exception $exception
	 */
	public function __construct(\Exception $exception){
		parent::__construct($exception->getMessage());
	}

}