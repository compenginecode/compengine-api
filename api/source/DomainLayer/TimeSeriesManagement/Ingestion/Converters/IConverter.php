<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\Converters;

/**
 * Class IConverter
 * @package DomainLayer\TimeSeriesManagement\Ingestion\Converters
 */
interface IConverter {

	/** convertToTimeSeries
	 *
	 * 	Converts the given file to an array of floats.
	 *  The array itself represents the time series data.
	 *
	 * @param $filePath
	 * @return array of float
	 */
	public function convertToTimeSeries($filePath);

	/** getConversionType
	 *
	 * 	Returns a string that denotes the type of conversion
	 * 	process supplied.
	 *
	 * @return string
	 */
	public function getConversionType();

}