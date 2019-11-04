<?php

namespace DomainLayer\ORM\TimeSeries\IngestedTimeSeries;

use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\ORM\Fingerprint\Fingerprint;
use DomainLayer\ORM\TimeSeries\RawTimeSeries\RawTimeSeries;

/**
 * Class IngestedTimeSeries
 * @package DomainLayer\ORM\TimeSeries\IngestedTimeSeries
 */
class IngestedTimeSeries

	extends RawTimeSeries
	implements IIngestedTimeSeries{

	/** $hashFamily
	 *
	 * 	Associative array of "hashes" used to compare this time
	 * 	series with others.
	 *
	 * @var array
	 */
	protected $fingerprint;

	/** $rawFeatureVector
	 *
	 * 	The raw feature vector
	 *
	 * @var FeatureVector
	 */
	protected $rawFeatureVector;

	/** $normalizedFeatureVector
	 *
	 * 	The normalized feature vector
	 *
	 * @var FeatureVector
	 */
	protected $normalizedFeatureVector;

	/**
	 * IngestedTimeSeries constructor.
	 * @param array $dataPoints
	 * @param FeatureVector $rawFeatureVector
	 * @param FeatureVector $normalizedFeatureVector
	 * @param Fingerprint $fingerprint
	 */
	public function __construct(array $dataPoints, FeatureVector $rawFeatureVector,
		FeatureVector $normalizedFeatureVector, Fingerprint $fingerprint) {

		$this->rawFeatureVector = $rawFeatureVector;
		$this->normalizedFeatureVector = $normalizedFeatureVector;
		$this->fingerprint = $fingerprint;

		parent::__construct($dataPoints);

	}

	/** getRawFeatureVector
	 *
	 * 	Returns the raw feature vector.
	 *
	 * @return FeatureVector
	 */
	public function getRawFeatureVector(){
		return $this->rawFeatureVector;
	}

	/** getNormalizedFeatureVector
	 *
	 * 	Returns the normalized feature vector.
	 *
	 * @return FeatureVector
	 */
	public function getNormalizedFeatureVector(){
		return $this->normalizedFeatureVector;
	}

	/**
	 * @param FeatureVector $normalizedFeatureVector
	 */
	public function setNormalizedFeatureVector($normalizedFeatureVector) {
		$this->normalizedFeatureVector = $normalizedFeatureVector;
	}

	/**
	 * @param FeatureVector $rawFeatureVector
	 */
	public function setRawFeatureVector($rawFeatureVector) {
		$this->rawFeatureVector = $rawFeatureVector;
	}

	/**
	 * @param Fingerprint $fingerprint
	 */
	public function setFingerprint(Fingerprint $fingerprint) {
		$this->fingerprint = $fingerprint;
	}

	/**
	 * @return array|Fingerprint
	 */
	public function getFingerprint() {
		return $this->fingerprint;
	}

}