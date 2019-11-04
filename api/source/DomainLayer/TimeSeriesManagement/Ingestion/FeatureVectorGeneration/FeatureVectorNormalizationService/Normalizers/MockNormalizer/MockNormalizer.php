<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers\MockNormalizer;

use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers\INormalizer;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\MappingManifest\MappingManifest;

/**
 * Class MockNormalizer
 * @package DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers\MockNormalizer
 */
class MockNormalizer implements INormalizer{

	/** normalize
	 *
	 * 	Normalizes the given feature vector against the given mapping manifest. Note that this
	 * 	function returns the new, normalized vector leaving the original untouched.
	 *
	 * 	This is used for mocking the normalization process.
	 *
	 * @param MappingManifest $manifest
	 * @param FeatureVector $rawFeatureVector
	 * @return FeatureVector
	 * @throws \DomainLayer\ORM\FeatureVector\Exceptions\EFeatureElementMappingIdNotPresent
	 */
	public function normalize(MappingManifest $manifest, FeatureVector $rawFeatureVector){
		return $rawFeatureVector;
	}

}