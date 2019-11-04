<?php

namespace DomainLayer\TimeSeriesManagement\SpecialFeatureIdentification\Percentile;

use DomainLayer\ORM\FeatureVectorDescriptor\FeatureVectorDescriptor;

/**
 * Class Percentile
 * @package DomainLayer\TimeSeriesManagement\SpecialFeatureIdentification\Percentile
 */
class Percentile {

	/** $featureVectorDescriptor
	 *
	 * 	The feature vector descriptor that this percentile represents.
	 *
	 * @var FeatureVectorDescriptor
	 */
	private $featureVectorDescriptor;

	/** $percentile
	 *
	 * 	The SFI value of this percentile.
	 *
	 * @var float
	 */
	private $percentile;

	/** __construct
	 *
	 * 	Percentile constructor.
	 *
	 * @param FeatureVectorDescriptor $featureVectorDescriptor
	 * @param $percentile
	 */
	public function __construct(FeatureVectorDescriptor $featureVectorDescriptor, $percentile) {
		$this->featureVectorDescriptor = $featureVectorDescriptor;
		$this->percentile = $percentile;
	}

	/**
	 * @return FeatureVectorDescriptor
	 */
	public function getFeatureVectorDescriptor() {
		return $this->featureVectorDescriptor;
	}

	/**
	 * @return float
	 */
	public function getPercentile() {
		return $this->percentile;
	}

}