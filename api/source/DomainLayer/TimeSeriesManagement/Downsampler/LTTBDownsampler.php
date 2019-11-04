<?php

namespace DomainLayer\TimeSeriesManagement\Downsampler;

use Webit\DownSampling\DownSampler\LargestTriangleThreeBucketsDownSampler;

/**
 * Class LTTBDownsampler
 * @package DomainLayer\TimeSeriesManagement\Downsampler
 */
class LTTBDownsampler implements IDownsampler{

	/** downsample
	 *
	 * 	Downsamples the given array to the desired length using the Largest Triangle Three Bucket
	 * 	method.
	 *
	 * @notes http://skemman.is/stream/get/1946/15343/37285/3/SS_MSthesis.pdf
	 * @param array $dataPoints
	 * @param $length
	 * @return array
	 */
	public function downsample(array $dataPoints, $length){
		$downsampler = new LargestTriangleThreeBucketsDownSampler();
		return $downsampler->sampleDown($dataPoints, $length);
	}

}