<?php

namespace DomainLayer\TimeSeriesManagement\Diagnostics;

use DomainLayer\ORM\FeatureVectorDescriptor\FeatureVectorDescriptor;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use InfrastructureLayer\ElasticSearch\ElasticSearch;

/**
 * Class CentralMeasureDiagnosticService
 * @package DomainLayer\TimeSeriesManagement\Diagnostics
 */
class CentralMeasureDiagnosticService {

	private $siteAttributeRepository;

	private $elasticSearch;

	private function getHistogram(FeatureVectorFamily $featureVectorFamily){
		$mappingIds = [];
		foreach($featureVectorFamily->descriptors() as $aDescriptor){
			/** @var $aDescriptor FeatureVectorDescriptor */
			$mappingIds[$aDescriptor->getMappingId()] = $aDescriptor->getName();
		}

		$centralMeasures = [];
		$centralMeasures = $this->elasticSearch->getRawFeatureVectorHistogram(
			$featureVectorFamily->getIndexName(),
			array_keys($mappingIds)
		);

		$output = array();
		foreach($centralMeasures as $aMappingId => $aHistogram){
			$cleanedHistogram = array();
			foreach($aHistogram as $aPartition){
				$cleanedHistogram[] = array(
					"x" => (int)$aPartition["partition"],
					"y" => (float)$aPartition["count"],
				);
			}

			$output[] = array(
				"name" => $mappingIds[$aMappingId],
				"mappingId" => $aMappingId,
				"histogram" => $cleanedHistogram
			);
		}

		return $output;
	}

	private function getPercentiles(FeatureVectorFamily $featureVectorFamily){
		$mappingIds = [];
		foreach($featureVectorFamily->descriptors() as $aDescriptor){
			/** @var $aDescriptor FeatureVectorDescriptor */
			$mappingIds[$aDescriptor->getMappingId()] = $aDescriptor->getName();
		}

		$centralMeasures = $this->elasticSearch->getRawFeatureVectorPercentiles(
			$featureVectorFamily->getIndexName(),
			array_keys($mappingIds)
		);

		$output = array();
		foreach($centralMeasures as $aFeatureElementMappingId => $percentiles){
			$output[$aFeatureElementMappingId] = array(
				"iqr" => round((float)$percentiles["75.0"] - (float)$percentiles["25.0"], 3),
				"median" => round((float)$percentiles["50.0"], 3)
			);
		}

		return $output;
	}

	public function __construct(ISiteAttributeRepository $siteAttributeRepository, ElasticSearch $elasticSearch){
		$this->siteAttributeRepository = $siteAttributeRepository;
		$this->elasticSearch = $elasticSearch;
	}

	public function getDiagnosticInformation(){
		$featureVectorFamily = $this->siteAttributeRepository->getCurrentFeatureVectorFamily();
		$percentiles = $this->getPercentiles($featureVectorFamily);
		$histogram = $this->getHistogram($featureVectorFamily);

		return array(
			"percentiles" => $percentiles,
			"histograms" => $histogram
		);

	}

}