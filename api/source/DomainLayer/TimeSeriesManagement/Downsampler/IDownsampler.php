<?php

namespace DomainLayer\TimeSeriesManagement\Downsampler;

/**
 * Interface IDownsampler
 * @package DomainLayer\TimeSeriesManagement\Downsampler
 */
interface IDownsampler {

	/** downsample
	 *
	 * 	Downsamples the given array to the desired length.
	 *
	 * @param array $dataPoints
	 * @param $length
	 * @return array
	 */
	public function downsample(array $dataPoints, $length);

}