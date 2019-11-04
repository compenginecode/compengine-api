<?php

namespace DomainLayer\ORM\TimeSeries\RawTimeSeries;

/**
 * Interface IRawTimeSeries
 * @package DomainLayer\ORM\TimeSeries\RawTimeSeries
 */
interface IRawTimeSeries {

	/** getDataPoints
	 *
	 * 	Returns an array of data points.
	 *
	 * @return array
	 */
	public function getDataPoints();

}