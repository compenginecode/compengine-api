<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\Preprocessors;

/**
 * Interface IPreprocessor
 * @package DomainLayer\TimeSeriesManagement\Ingestion\Preprocessors
 */
interface IPreprocessor {

	/** preProcessTimeSeries
	 *
	 * 	Pre-processes the given time series and returns the new version.
	 *
	 * @param array $timeSeries
	 * @return array
	 */
	public function preProcessTimeSeries(array $timeSeries);

	/** requiresPreprocessing
	 *
	 * 	Returns TRUE if the time series requires preprocessing and FALSE otherwise.
	 *
	 * @param array $timeSeries
	 * @return bool
	 */
	public function requiresPreprocessing(array $timeSeries);

}