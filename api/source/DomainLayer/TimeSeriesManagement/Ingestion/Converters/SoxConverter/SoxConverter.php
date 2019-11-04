<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\Converters\SoxConverter;

use DomainLayer\TimeSeriesManagement\Ingestion\Converters\IAudioConverter;
use DomainLayer\TimeSeriesManagement\Ingestion\Converters\IConverter;
use DomainLayer\TimeSeriesManagement\Ingestion\Converters\SoxConverter\Exceptions\ESoxError;
use Jasny\Audio\Waveform;

/**
 * Class SoxConverter
 * @package DomainLayer\TimeSeriesManagement\Ingestion\Converters\SoxConverter
 */
class SoxConverter implements IConverter, IAudioConverter{

	/** convertToTimeSeries
	 *
	 * 	Converts the given file to an array of floats.
	 *  The array itself represents the time series data.
	 *
	 * @param $filePath
	 * @throws ESoxError
	 * @return array of float
	 */
	public function convertToTimeSeries($filePath){
        $waveform = new Waveform($filePath);
        return $waveform->getSamples();

        /** Old */
		try{
			$waveform = new Waveform($filePath);
			return $waveform->getSamples();
		}catch(\Exception $exception){
			throw new ESoxError($exception);
		}
	}

	/** getConversionType
	 *
	 * 	Returns a string that denotes the type of conversion
	 * 	process supplied.
	 *
	 * @return string
	 */
	public function getConversionType(){
		return "audio";
	}

}