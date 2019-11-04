<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\MappingManifestGenerationService;

use DomainLayer\ORM\FeatureVectorFamily\Repository\IFeatureVectorFamilyRepository;
use DomainLayer\ORM\SiteAttribute\Repository\DatabaseSiteAttributeRepository;
use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\MappingManifest\MappingManifest;

/**
 * Class MappingManifestGenerationService
 * @package DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\MappingManifestGenerationService
 */
class MappingManifestGenerationService {

	/**
	 * @var ISiteAttributeRepository
	 */
	private $databaseSiteAttributeRepository;


	public function __construct(ISiteAttributeRepository $databaseSiteAttributeRepository){
		$this->databaseSiteAttributeRepository = $databaseSiteAttributeRepository;
	}

	public function generateMappingManifestForCurrentFVFamily(){
		$currentFVFamily = $this->databaseSiteAttributeRepository->getCurrentFeatureVectorFamily();
		return new MappingManifest($currentFVFamily);
	}

}