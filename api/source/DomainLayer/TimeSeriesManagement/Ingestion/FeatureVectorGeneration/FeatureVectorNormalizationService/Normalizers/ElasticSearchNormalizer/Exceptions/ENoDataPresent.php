<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers\ElasticSearchNormalizer\Exceptions;

/**
 * Class ENoDataPresent
 * @package DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers\ElasticSearchNormalizer\Exceptions
 */
class ENoDataPresent extends \Exception{

	/** __construct
	 *
	 * 	ENoDataPresent constructor.
	 *
	 * @param $indexId
	 * @param $featureVectorElementMappingId
	 */
	public function __construct($indexId, $featureVectorElementMappingId){
		parent::__construct("The request for percentile data in index '$indexId' with " .
			"mapping ID '$featureVectorElementMappingId' returned no data.");
	}

}