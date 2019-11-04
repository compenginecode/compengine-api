<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers;

use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\MappingManifest\MappingManifest;

/**
 * Interface INormalizer
 * @package DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers
 */
interface INormalizer {

	/** normalize
	 *
	 * 	Normalizes the given feature vector against the given mapping manifest. Note that this
	 * 	function returns the new, normalized vector leaving the original untouched.
	 *
	 * @param MappingManifest $manifest
	 * @param FeatureVector $rawFeatureVector
	 * @return FeatureVector
	 * @throws \DomainLayer\ORM\FeatureVector\Exceptions\EFeatureElementMappingIdNotPresent
	 */
	public function normalize(MappingManifest $manifest, FeatureVector $rawFeatureVector);

}