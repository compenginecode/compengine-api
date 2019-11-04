<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\Converters\CSVConverter;

use DomainLayer\TimeSeriesManagement\Ingestion\Converters\Exceptions\EParseConversionError;
use DomainLayer\TimeSeriesManagement\Ingestion\Converters\IConverter;

/**
 * Class CSVConverter
 * @package DomainLayer\TimeSeriesManagement\Ingestion\Converters\CSVConverter
 */
class CSVConverter implements IConverter{

	/** detectDelimiter
	 *
	 * 	Attempts to detect the delimiter from the given file. If no delimiter can be
	 *	found successfully, an exception of type EParseConversionError is thrown. 
	 *
	 * @notes Use Ord() to observe the delimiters returned as they may be non-printable ASCII.
	 * @param $fileContents
	 * @return string
	 * @throws EParseConversionError
	 */
	protected function detectDelimiter($fileContents){

		/** Array of possible delimiters. Ensure subset keys are placed
		 * 	below their supersets to avoid collisions. */
		$possibleDelimiters = array(
			"," => 0,
			"\t" => 0,
			"\n" => 0
		);

		foreach($possibleDelimiters as $aDelimiter => &$count){
			$count = substr_count($fileContents, $aDelimiter);
		}

		/** The sum of values will be zero if there's no count for any of them. */
		if (0 === array_sum($possibleDelimiters)){
			throw new EParseConversionError("Could not determine the delimiter.");
		}

		return array_search(max($possibleDelimiters), $possibleDelimiters);
	}

	/** convertToTimeSeries
	 *
	 * 	Converts the given file to an array of string floats.
	 *  The array itself represents the time series data.
	 *
	 * @param $filePath
	 * @return array of float
	 */
	public function convertToTimeSeries($filePath){
		$fileContents = file_get_contents($filePath);
		$delimiter = $this->detectDelimiter($fileContents);

		$results = [];
		$file = fopen($filePath, "r");
		while (($line = fgetcsv($file, 0, $delimiter)) !== FALSE){
			$cleaned = [];
			foreach($line as $anElement){
				if (is_numeric($anElement)){
					$cleaned[] = $anElement;
				}
			}
			$results = array_merge($results, $cleaned);
		}

		fclose($file);

		return $results;
	}

	/** getConversionType
	 *
	 * 	Returns a string that denotes the type of conversion
	 * 	process supplied.
	 *
	 * @return string
	 */
	public function getConversionType(){
		return "csv";
	}

}