<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester\Exceptions;

/**
 * Class EUnsupportedFileExtension
 * @package DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester\Exceptions
 */
class EUnsupportedFileExtension extends \Exception{

	/** __construct
	 *
	 * 	EUnsupportedFileExtension constructor.
	 *
	 * @param string $fileExtension
	 */
	public function __construct($fileExtension){
		parent::__construct("The file extension '$fileExtension' has no supported IConverter interface
			registered in the TimeSeriesIngester class.");
	}

}