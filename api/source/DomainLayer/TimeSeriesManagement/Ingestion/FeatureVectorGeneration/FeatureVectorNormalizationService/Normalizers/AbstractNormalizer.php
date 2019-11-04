<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers;

use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\MappingManifest\MappingManifest;

/**
 * Class AbstractNormalizer
 * @package DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers
 */
abstract class AbstractNormalizer implements INormalizer{

	/** getInterQuartileRange
	 *
	 * 	Returns the inter-quartile range of the given distribution of the given feature vector element.
	 *
	 * @param FeatureVectorFamily $featureVectorFamily
	 * @param $featureElementMappingId
	 * @return float
	 */
	abstract protected function getInterQuartileRange(FeatureVectorFamily $featureVectorFamily, $featureElementMappingId);

	/** getMedian
	 *
	 * 	Returns the median of the given distribution of the given feature vector element.
	 *
	 * @param FeatureVectorFamily $featureVectorFamily
	 * @param $featureElementMappingId
	 * @return float
	 */
	abstract protected function getMedian(FeatureVectorFamily $featureVectorFamily, $featureElementMappingId);

	/** prepare
	 *
	 * 	Called before the loop that starts requests to getInterQuartileRange and getMedian. Useful for derived types
	 * 	as they can make batch calls and prepare for the method hits.
	 *
	 * @virtual
	 * @param MappingManifest $mappingManifest
	 * @return bool
	 */
	protected function prepare(MappingManifest $mappingManifest){
		return TRUE;
	}

	/** normalize
	 *
	 * 	Normalizes the given feature vector against the given mapping manifest. Note that this
	 * 	function returns the new, normalized vector leaving the original untouched.
	 *
	 * @param MappingManifest $mappingManifest
	 * @param FeatureVector $rawFeatureVector
	 * @return FeatureVector
	 * @throws \DomainLayer\ORM\FeatureVector\Exceptions\EFeatureElementMappingIdNotPresent
	 */
	public function normalize(MappingManifest $mappingManifest, FeatureVector $rawFeatureVector){
		/** We need to normalize $rawValue against the population and return a normalized value  */
		$normalizedFeatureVector = new FeatureVector();

		$this->prepare($mappingManifest);
		foreach($mappingManifest->featureElementMappingIds() as $aFeatureElementMappingId){
			/** Obtain all the statistical measures and original raw value */
			$iqr = $this->getInterQuartileRange($mappingManifest->getFeatureVectorFamily(), $aFeatureElementMappingId);
			$median = $this->getMedian($mappingManifest->getFeatureVectorFamily(), $aFeatureElementMappingId);
			$rawValue = $rawFeatureVector->getElementValue($aFeatureElementMappingId);

			if (empty($iqr)){
				/** An IRQ of zero means we have a division of zero. Don't normalize (only one element!) */
				$normalizedValue = $rawValue;
			}else{
				$normalizedValue = ($rawValue - $median) / (1.35 * $iqr);
			}
			$normalizedFeatureVector->addElementValue($aFeatureElementMappingId, $normalizedValue);
		}

		return $normalizedFeatureVector;
	}

}