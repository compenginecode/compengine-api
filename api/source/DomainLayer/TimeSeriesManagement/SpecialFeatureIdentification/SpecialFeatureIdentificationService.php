<?php

namespace DomainLayer\TimeSeriesManagement\SpecialFeatureIdentification;

use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\ORM\FeatureVectorDescriptor\FeatureVectorDescriptor;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\TimeSeriesManagement\SpecialFeatureIdentification\Percentile\Percentile;
use InfrastructureLayer\ElasticSearch\ElasticSearch;

/**
 * Class SpecialFeatureIdentificationService
 * @package DomainLayer\TimeSeriesManagement\SpecialFeatureIdentification
 */
class SpecialFeatureIdentificationService {

	private $elasticSearch;

	public function __construct(ElasticSearch $elasticSearch){
		$this->elasticSearch = $elasticSearch;
	}

	public function findSpecialFeatures(FeatureVector $normalizedFeatureVector, FeatureVectorFamily $featureVectorFamily){
		$mappingIds = [];
		foreach($featureVectorFamily->descriptors() as $aDescriptor){
			/** @var $aDescriptor FeatureVectorDescriptor */
			$mappingIds[$aDescriptor->getMappingId()] = $aDescriptor->getMappingId();
		}

		$response = $this->elasticSearch->getSpecialFeatureIdentifiers(
			$featureVectorFamily->getIndexName(),
			$mappingIds,
			$normalizedFeatureVector
		);

		$percentiles = [];
		foreach($response as $aMappingId => $aValue){
			$fvDescriptor = $featureVectorFamily->descriptors()->getByMappingId($aMappingId);
			$percentiles[] = new Percentile($fvDescriptor, $aValue);
		}

		return $percentiles;
	}

}