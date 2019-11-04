<?php

namespace DomainLayer\TimeSeriesManagement\Comparison\ComparisonStatus;

use DomainLayer\Common\Enum\Enum;

/**
 * Class ComparisonStatus
 * @package DomainLayer\TimeSeriesManagement\Comparison\ComparisonStatus
 */
class ComparisonStatus extends Enum{

	const STATUS_IDLE = "0x000";
	const STATUS_PROCESS_STARTED = "0x001";
	const STATUS_PROCESS_FINISHED = "0x002";
	const STATUS_CONVERSION_STARTED = "0x003";
	const STATUS_CONVERSION_FINISHED = "0x004";
	const STATUS_PRE_PROCESSING_STARTED = "0x005";
	const STATUS_PRE_PROCESSING_FINISHED = "0x006";

	private $conversionType = NULL;

	/** __construct
	 *
	 * 	ComparisonStatus constructor.
	 *
	 * @param $value
	 * @param $conversionType
	 */
	protected function __construct($value, $conversionType) {
		parent::__construct($value);
		$this->conversionType = $conversionType;
	}

	/** idle
	 *
	 * 	Returns a new instance of this class with the enum value
	 * 	of STATUS_IDLE.
	 *
	 * @static
	 * @return ComparisonStatus
	 */
	public static function idle(){
		return new self(self::STATUS_IDLE, NULL);
	}

	/** processStarted
	 *
	 * 	Returns a new instance of this class with the enum value
	 * 	of STATUS_PROCESS_STARTED.
	 *
	 * @static
	 * @return ComparisonStatus
	 */
	public static function processStarted(){
		return new self(self::STATUS_PROCESS_STARTED, NULL);
	}

	/** processFinished
	 *
	 * 	Returns a new instance of this class with the enum value
	 * 	of STATUS_PROCESS_FINISHED.
	 *
	 * @static
	 * @return ComparisonStatus
	 */
	public static function processFinished(){
		return new self(self::STATUS_PROCESS_FINISHED, NULL);
	}

	/** conversionStarted
	 *
	 * 	Returns a new instance of this class with the enum value
	 * 	of STATUS_CONVERSION_STARTED.
	 *
	 * @static
	 * @return ComparisonStatus
	 */
	public static function conversionStarted($type){
		return new self(self::STATUS_CONVERSION_STARTED, $type);
	}

	/** conversionFinished
	 *
	 * 	Returns a new instance of this class with the enum value
	 * 	of STATUS_CONVERSION_FINISHED.
	 *
	 * @static
	 * @return ComparisonStatus
	 */
	public static function conversionFinished(){
		return new self(self::STATUS_CONVERSION_FINISHED, NULL);
	}

	/** preprocessingStarted
	 *
	 * 	Returns a new instance of this class with the enum value
	 * 	of STATUS_PRE_PROCESSING_STARTED.
	 *
	 * @static
	 * @return ComparisonStatus
	 */
	public static function preprocessingStarted(){
		return new self(self::STATUS_PRE_PROCESSING_STARTED, NULL);
	}

	/** preprocessingFinished
	 *
	 * 	Returns a new instance of this class with the enum value
	 * 	of STATUS_PRE_PROCESSING_FINISHED.
	 *
	 * @static
	 * @return ComparisonStatus
	 */
	public static function preprocessingFinished(){
		return new self(self::STATUS_PRE_PROCESSING_FINISHED, NULL);
	}

	/**
	 * @return string
	 */
	public function getConversionType(){
		return $this->conversionType;
	}

}