<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorHashService;

use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\ORM\Fingerprint\Fingerprint;
use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\ORM\TopLevelCategory\TopLevelCategory;

/**
 * Class FeatureVectorHashService
 * @package DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorHashService
 */
class FeatureVectorHashService {

	private $siteAttributeRepository;

	public function __construct(ISiteAttributeRepository $siteAttributeRepository){
		$this->siteAttributeRepository = $siteAttributeRepository;
	}

	/**
	 * @param FeatureVector $featureVector
	 * @return Fingerprint
	 * @throws \DomainLayer\ORM\FeatureVectorIndex\HashTableCollection\Exceptions\EPersistBeforeGenerateHash
	 */
	public function generateFingerprint(FeatureVector $featureVector, TopLevelCategory $topLevelCategory){
		$commonFingerprint = $this->siteAttributeRepository
			->getCurrentFeatureVectorFamily()
			->getCommonIndex()
			->hashTables()
			->generateHash($featureVector->toVector()
		);

		if ($topLevelCategory->equals(TopLevelCategory::CATEGORY_REAL)){
			$index = $syntheticHash = $this->siteAttributeRepository->getCurrentFeatureVectorFamily()->getRealIndex();
			$categoryFingerprint = $index->hashTables()->generateHash($featureVector->toVector());
		}else if ($topLevelCategory->equals(TopLevelCategory::CATEGORY_SYNTHETIC)){
			$index = $syntheticHash = $this->siteAttributeRepository->getCurrentFeatureVectorFamily()->getSyntheticIndex();
			$categoryFingerprint = $index->hashTables()->generateHash($featureVector->toVector());
		}else{
			$categoryFingerprint = [];
		}

		return new Fingerprint($commonFingerprint, $categoryFingerprint);
	}

}