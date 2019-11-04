<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService;

use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers\INormalizer;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\MappingManifest\MappingManifest;

/**
 * Class FeatureVectorNormalizationService
 * @package DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService
 */
class FeatureVectorNormalizationService {

	/** $normalizer
	 *
	 * 	Interface to a normalizer that is used to normalize feature vectors.
	 * 	The concretion is controlled by PHP-DI and specified in the
	 * 	Dependency Injection Overrides.
	 *
	 * @var INormalizer
	 */
	private $normalizer;

	/** __construct
	 *
	 * 	FeatureVectorNormalizationService constructor.
	 *
	 * @param INormalizer $normalizer
	 */
	public function __construct(INormalizer $normalizer){
		$this->normalizer = $normalizer;
	}

	/** normalizeFeatureVector
	 *
	 * 	Returns a normalized feature vector.
	 *
	 * @param FeatureVector $rawFeatureVector
	 * @param MappingManifest $mappingManifest
	 * @return FeatureVector
	 */
	public function normalizeFeatureVector(FeatureVector $rawFeatureVector, MappingManifest $mappingManifest){
		return $this->normalizer->normalize($mappingManifest, $rawFeatureVector);
	}

}