<?php

namespace DomainLayer\ORM\FeatureVectorDocument;

use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;

/**
 * Class FeatureVectorDocument
 * @package DomainLayer\ORM\FeatureVectorDocument
 */
class FeatureVectorDocument {

	/**
	 * @var FeatureVector
	 */
	private $rawFeatureVector;

	/**
	 * @var FeatureVector
	 */
	private $normalizedFeatureVector;

	/**
	 * @var array
	 */
	private $hashFamily;

	/**
	 * @var null
	 */
	private $timeSeriesId = NULL;

	private $indexKey = NULL;

	/**
	 * FeatureVectorDocument constructor.
	 * @param FeatureVector $rawFeatureVector
	 * @param FeatureVector $normalizedFeatureVector
	 * @param array $hashFamily
	 * @param null $timeSeriesId
	 * @param null $indexKey
	 */
	public function __construct(FeatureVector $rawFeatureVector, FeatureVector $normalizedFeatureVector,
		array $hashFamily = [], $timeSeriesId = NULL, $indexKey = NULL){

		$this->rawFeatureVector = $rawFeatureVector;
		$this->normalizedFeatureVector = $normalizedFeatureVector;
		$this->hashFamily = $hashFamily;
		$this->timeSeriesId = $timeSeriesId;
		$this->indexKey = $indexKey;
	}

	public static function fromArray(array $array, $indexKey = NULL){
		$rawFeatureVector = FeatureVector::fromArray($array["rawFeatureVector"]);
		$normalizedFeatureVector = FeatureVector::fromArray($array["normalizedFeatureVector"]);
		$hashFamily = $array["hashFamily"];
		$timeSeriesId = $array["timeSeriesId"];

		return new self($rawFeatureVector, $normalizedFeatureVector, $hashFamily, $timeSeriesId, $indexKey);
	}

	/**
	 * @return array
	 */
	public function toArray(){
		return array(
			"timeSeriesId" => $this->timeSeriesId,
			"rawFeatureVector" => $this->rawFeatureVector->toArray(),
			"normalizedFeatureVector" => $this->normalizedFeatureVector->toArray(),
			"hashFamily" => $this->hashFamily
		);
 	}

	/**
	 * @return FeatureVector
	 */
	public function getRawFeatureVector() {
		return $this->rawFeatureVector;
	}

	/**
	 * @return FeatureVector
	 */
	public function getNormalizedFeatureVector() {
		return $this->normalizedFeatureVector;
	}

	public function setOwningTimeSeriesId(PersistedTimeSeries $timeSeries){
		$this->timeSeriesId = $timeSeries->getId();
	}

	/**
	 * @return null|string
	 */
	public function getTimeSeriesId() {
		return $this->timeSeriesId;
	}

	/**
	 * @return array
	 */
	public function getHashFamily() {
		return $this->hashFamily;
	}

	/**
	 * @return string
	 */
	public function getIndexKey() {
		return $this->indexKey;
	}

}