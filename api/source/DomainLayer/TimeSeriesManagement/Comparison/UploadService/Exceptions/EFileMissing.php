<?php

namespace DomainLayer\TimeSeriesManagement\Comparison\UploadService\Exceptions;

/**
 * Class EFileMissing
 * @package DomainLayer\TimeSeriesManagement\Comparison\UploadService\Exceptions
 */
class EFileMissing extends \Exception{

	/** __construct
	 *
	 * 	EFileMissing constructor.
	 *
	 * @param string $formKey
	 */
	public function __construct($formKey){
		parent::__construct("The key '$formKey' was not found in the FILES super global.");
	}

}