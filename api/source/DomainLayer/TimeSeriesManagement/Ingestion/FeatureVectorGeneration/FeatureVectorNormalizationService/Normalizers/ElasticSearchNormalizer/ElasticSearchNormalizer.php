<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers\ElasticSearchNormalizer;

use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers\AbstractNormalizer;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers\ElasticSearchNormalizer\Exceptions\ENoDataPresent;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\MappingManifest\MappingManifest;
use InfrastructureLayer\ElasticSearch\ElasticSearch;

/**
 * Class ElasticSearchNormalizer
 * @package DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\Normalizers\ElasticSearchNormalizer
 */
class ElasticSearchNormalizer extends AbstractNormalizer{

	/** $elasticSearch
	 *
	 * 	Interface to an ElasticSearch gateway.
	 *
	 * @var ElasticSearch
	 */
	private $elasticSearch;

	/** $percentiles
	 *
	 * 	Array of percentile information, filled when the prepare()
	 * 	method is called. Must be filled before getInterQuartileRange()
	 * 	and getMedian() methods are called.
	 *
	 * @var array
	 */
	private $percentiles = [];

	/** getInterQuartileRange
	 *
	 * 	Returns the inter-quartile range of the given distribution of the given feature vector element.
	 *
	 * @param FeatureVectorFamily $featureVectorFamily
	 * @param $featureElementMappingId
	 * @return float
	 * @throws ENoDataPresent
	 */
	protected function getInterQuartileRange(FeatureVectorFamily $featureVectorFamily, $featureElementMappingId){
		if (isset($this->percentiles[$featureElementMappingId])
			&& isset($this->percentiles[$featureElementMappingId]["75.0"])
			&& isset($this->percentiles[$featureElementMappingId]["25.0"])){

			if (is_numeric($this->percentiles[$featureElementMappingId]["25.0"])
				&& is_numeric($this->percentiles[$featureElementMappingId]["75.0"])){

				return $this->percentiles[$featureElementMappingId]["75.0"]
					- $this->percentiles[$featureElementMappingId]["25.0"];
			}else{
				return 0;
			}
		}

		throw new ENoDataPresent($featureVectorFamily->getIndexName(), $featureElementMappingId);
	}

	/** getMedian
	 *
	 * 	Returns the median of the given distribution of the given feature vector element.
	 *
	 * @param FeatureVectorFamily $featureVectorFamily
	 * @param $featureElementMappingId
	 * @return float
	 * @throws ENoDataPresent
	 */
	protected function getMedian(FeatureVectorFamily $featureVectorFamily, $featureElementMappingId){
		if (isset($this->percentiles[$featureElementMappingId])
			&& isset($this->percentiles[$featureElementMappingId]["50.0"])){

			if (is_numeric($this->percentiles[$featureElementMappingId]["50.0"])){
				return $this->percentiles[$featureElementMappingId]["50.0"];
			}else{
				return 0;
			}
		}

		throw new ENoDataPresent($featureVectorFamily->getIndexName(), $featureElementMappingId);
	}

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
		$this->percentiles = $this->elasticSearch->getRawFeatureVectorPercentiles(
			$mappingManifest->getFeatureVectorFamily()->getIndexName(),
			$mappingManifest->featureElementMappingIds()
		);
	}

	/** __construct
	 *
	 * 	ElasticSearchNormalizer constructor.
	 *
	 * @param ElasticSearch $elasticSearch
	 */
	public function __construct(ElasticSearch $elasticSearch){
		$this->elasticSearch = $elasticSearch;
	}

}