<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\MappingManifest;
use DomainLayer\ORM\FeatureVectorDescriptor\FeatureVectorDescriptor;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;

/**
 * Class MappingManifest
 * @package DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\MappingManifest
 */
class MappingManifest {

	private $featureVectorFamily;

	protected $featureElementMappingIds = [];

	public function __construct(FeatureVectorFamily $featureVectorFamily){
		$this->featureVectorFamily = $featureVectorFamily;

		foreach($featureVectorFamily->descriptors()as $aDescriptor){
			/** @var $aDescriptor FeatureVectorDescriptor */
			$this->featureElementMappingIds[] = $aDescriptor->getMappingId();
		}
	}

	public function featureElementMappingIds(){
		return $this->featureElementMappingIds;
	}

	/**
	 * @return mixed
	 */
	public function getGeneratorScriptPath() {
		return $this->featureVectorFamily->getGeneratorScriptPath();
	}

	/**
	 * @return FeatureVectorFamily
	 */
	public function getFeatureVectorFamily() {
		return $this->featureVectorFamily;
	}

}