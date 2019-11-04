<?php

namespace DomainLayer\ORM\TimeSeries\RawTimeSeries;
use DomainLayer\ORM\DataPoints\DataPoints;

/**
 * Class RawTimeSeries
 * @package DomainLayer\ORM\TimeSeries\RawTimeSeries
 */
class RawTimeSeries implements IRawTimeSeries{

	/** $dataPoints
	 *
	 * 	Array of data points (floats).
	 *
	 * @var array
	 */
	protected $dataPoints = [];

	/** __construct
	 *
	 * 	RawTimeSeries constructor.
	 *
	 * @param array $dataPoints
	 */
	public function __construct(array $dataPoints){
		$casted = [];
		foreach($dataPoints as $aDataPoint){
			$casted[] = (float)$aDataPoint;
		}
		$this->dataPoints = new DataPoints($casted);
	}

	/** getDataPoints
	 *
	 * 	Returns an array of the data points of the time series.
	 *
	 * @return array
	 */
	public function getDataPoints() {
		$casted = [];
		foreach($this->dataPoints->getDataPoints() as $aDataPoint){
			$casted[] = (float)$aDataPoint;
		}

		return $casted;
	}

}