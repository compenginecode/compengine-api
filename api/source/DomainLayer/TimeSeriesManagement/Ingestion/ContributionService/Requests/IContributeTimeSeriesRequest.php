<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\ContributionService\Requests;

use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\Contributor\Contributor;
use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\ORM\FeatureVectorDocument\FeatureVectorDocument;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\ORM\Fingerprint\Fingerprint;
use DomainLayer\ORM\SamplingInformation\SamplingInformation;
use DomainLayer\ORM\Source\Source;

/**
 * Interface IContributeTimeSeriesRequest
 * @package DomainLayer\TimeSeriesManagement\Ingestion\ContributionService\Requests
 */
interface IContributeTimeSeriesRequest {

	/**	getName
	 *
	 * 	Returns the name of the time series.
	 *
	 * @return string
	 */
	public function getName();

	/**	getDescription
	 *
	 * 	Returns the description of the time series.
	 *
	 * @return string
	 */
	public function getDescription();

	/** getSource
	 *
	 * 	Returns the source associated to the time series. Returns NULL if none
	 * 	is supplied.
	 *
	 * @return Source|NULL
	 */
	public function getSource();

	/** getSamplingInformation
	 *
	 * 	Returns the sampling information associated to the time series.
	 *
	 * @return SamplingInformation
	 */
	public function getSamplingInformation();

	/** getCategory
	 *
	 * 	Returns the category associated to the time series.
	 *
	 * @return Category
	 */
	public function getCategory();

	/** getTags
	 *
	 * 	Returns all the tags sassociated with the time series.
	 *
	 * @return array
	 */
	public function getTags();

	/**	getDataPoints
	 *
	 * 	Returns an array of data points.
	 *
	 * @return array
	 */
	public function getDataPoints();

	/**	getRawFeatureVector
	 *
	 * 	Returns the raw feature vector.
	 *
	 * @return FeatureVector
	 */
	public function getRawFeatureVector();

	/**	getNormalizedFeatureVector
	 *
	 * 	Returns the normalized feature vector.
	 *
	 * @return FeatureVector
	 */
	public function getNormalizedFeatureVector();

	/**	getHashFamily
	 *
	 * 	Returns the array of hashes.
	 *
	 * @return Fingerprint
	 */
	public function getHashFamily();

	/** getFeatureVectorFamily
	 *
	 * 	Returns the feature vector family to contribute the time series to.
	 *
	 * @return FeatureVectorFamily
	 */
	public function getFeatureVectorFamily();

	/**	getContributor
	 *
	 * 	Returns the contributor, if present.
	 *
	 * @return Contributor|NULL
	 */
	public function getContributor();

}