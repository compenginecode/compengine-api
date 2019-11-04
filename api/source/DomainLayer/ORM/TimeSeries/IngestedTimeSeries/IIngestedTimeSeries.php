<?php

namespace DomainLayer\ORM\TimeSeries\IngestedTimeSeries;

use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\ORM\TimeSeries\RawTimeSeries\IRawTimeSeries;

/**
 * Interface IIngestedTimeSeries
 * @package DomainLayer\ORM\TimeSeries\IngestedTimeSeries
 */
interface IIngestedTimeSeries extends IRawTimeSeries{

	/** getRawFeatureVector
	 *
	 * 	Returns the raw feature vector.
	 * 
	 * @return FeatureVector
	 */
	public function getRawFeatureVector();

	/** getNormalizedFeatureVector
	 *
	 * 	Returns the normalized feature vector.
	 *
	 * @return FeatureVector
	 */
	public function getNormalizedFeatureVector();

}