<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\Converters\Exceptions;

/**
 * Class EParseConversionError
 * @package DomainLayer\TimeSeriesManagement\Ingestion\Converters\Exceptions
 */
class EParseConversionError extends \Exception{

	/** __construct
	 *
	 * 	EParseConversionError constructor.
	 *
	 * @param string $message
	 */
	public function __construct($message){
		parent::__construct("Failed to parse file into a time series: $message");
	}

}