<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\Preprocessors\Truncator;

use DomainLayer\TimeSeriesManagement\Ingestion\Preprocessors\IPreprocessor;

/**
 * Class Truncator
 * @package DomainLayer\TimeSeriesManagement\Ingestion\Preprocessors\Truncator
 */
class Truncator implements IPreprocessor{

	/** getTruncationLimit
	 *
	 * 	Returns the truncation limit. Any time series values beyond this
	 * 	will be explicitly sliced out.
	 *
	 * @return int
	 */
	protected function getTruncationLimit(){
		return 10000;
	}

	/** preProcessTimeSeries
	 *
	 * 	Pre-processes the given time series and returns the new version.
	 *
	 * @param array $timeSeries
	 * @return array
	 */
	public function preProcessTimeSeries(array $timeSeries){
		return array_slice($timeSeries, 0, $this->getTruncationLimit());
	}

	/** requiresPreprocessing
	 *
	 * 	Returns TRUE if the time series requires preprocessing and FALSE otherwise.
	 *
	 * @param array $timeSeries
	 * @return bool
	 */
	public function requiresPreprocessing(array $timeSeries){
		return count($timeSeries) > $this->getTruncationLimit();
	}

}