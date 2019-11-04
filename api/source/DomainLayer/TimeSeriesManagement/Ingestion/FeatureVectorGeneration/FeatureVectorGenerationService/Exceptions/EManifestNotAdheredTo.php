<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorGenerationService\Exceptions;

/**
 * Class EManifestNotAdheredTo
 * @package DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorGenerationService\Exceptions
 */
class EManifestNotAdheredTo extends \Exception{

	/** __construct
	 *
	 * 	EManifestNotAdheredTo constructor.
	 *
	 * @param array $returnedMappingIds
	 * @param array $requiredMappingIds
	 */
	public function __construct(array $returnedMappingIds, array $requiredMappingIds){
		parent::__construct(
			"The result of the feature vector generator script does not match the requirements in the mapping manifest. " .
			"The script returned the mapping ids: " . implode(", ", $returnedMappingIds) . " yet the mapping manifest " .
			"required: " . implode(", ", $requiredMappingIds));
	}
}