<?php

namespace DomainLayer\ORM\SamplingInformation;

use DomainLayer\Common\Enum\Enum;

/**
 * Class SamplingInformation
 * @package DomainLayer\ORM\SamplingInformation
 *
 * 	This class is used to encapsulate the sampling information
 * 	that may (or may not) be present for a given time series.
 * 	By wrapping up the information in a Value Object we limit
 * 	exposure to incorrect field usage or integrity violations.
 *
 */
class SamplingInformation extends Enum{

	/** Enumeration string literal options */
	const SAMPLING_NOT_DEFINED = "not defined";
	const SAMPLING_DEFINED = "defined";

	/** $samplingRate
	 *
	 * 	The sampling rate. Only defined if the enum has a value of
	 * 	self::SAMPLING_DEFINED.
	 *
	 * @var string|NULL
	 */
	private $samplingRate;

	/** $samplingUnit
	 *
	 * 	The sampling unit. Only defined if the enum has a value of
	 * 	self::SAMPLING_DEFINED.
	 *
	 * @var string|NULL
	 */
	private $samplingUnit;

	/** __construct
	 *
	 * 	SamplingInformation constructor.
	 *
	 * @param mixed|NULL $enumValue
	 * @param NULL $samplingRate
	 * @param NULL $samplingUnit
	 */
	public function __construct($enumValue, $samplingRate = NULL, $samplingUnit = NULL){
		parent::__construct($enumValue);
		$this->samplingRate = $samplingRate;
		$this->samplingUnit = $samplingUnit;
	}

	/** notDefined
	 *
	 * 	Returns an instance of this class with enumeration value of
	 * 	self::SAMPLING_NOT_DEFINED.
	 *
	 * @return SamplingInformation
	 */
	public static function notDefined(){
		return new self(self::SAMPLING_NOT_DEFINED);
	}

	/** defined
	 *
	 * 	Returns an instance of this class with enumeration value of
	 * 	self::SAMPLING_DEFINED with the given sampling rate and sampling
	 * 	unit.
	 *
	 * @param string $samplingRate
	 * @param string $samplingUnit
	 * @return SamplingInformation
	 */
	public static function defined($samplingRate, $samplingUnit){
		return new self(self::SAMPLING_DEFINED, $samplingRate, $samplingUnit);
	}

	/** getSamplingUnit
	 *
	 * 	Returns the sampling unit.
	 *
	 * @return NULL|string
	 */
	public function getSamplingUnit() {
		return $this->samplingUnit;
	}

	/** getSamplingRate
	 *
	 * 	Returns the sampling rate.
	 *
	 * @return NULL|string
	 */
	public function getSamplingRate() {
		return $this->samplingRate;
	}

}