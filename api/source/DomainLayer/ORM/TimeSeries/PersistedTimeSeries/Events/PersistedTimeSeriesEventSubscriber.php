<?php

namespace DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Events;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\ORM\Fingerprint\Fingerprint;
use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\ORM\TopLevelCategory\TopLevelCategory;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorHashService\FeatureVectorHashService;
use InfrastructureLayer\ElasticSearch\ElasticSearch;

/**
 * Class PersistedTimeSeriesEventSubscriber
 * @package DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Events
 */
class PersistedTimeSeriesEventSubscriber implements EventSubscriber{

	/**
	 * @var ElasticSearch
	 */
	private $elasticSearch;

	private $siteAttributeRepository;

	private $featureVectorHashService;

	private function updateFingerprints(PersistedTimeSeries $persistedTimeSeries){
		/** We are now confident that we know the category of this time series, or at least the top-level category.
		 * 	As such, let's recompute the fingerprint before we save it. */
		$topLevelCategory = $persistedTimeSeries->getCategory()->getTopLevelCategory()->asTopLevelCategory();
		if ($topLevelCategory->equals(TopLevelCategory::CATEGORY_UNKNOWN)){
			throw new \Exception("Cannot persist a time series with a top level category of type CATEGORY_UNKNOWN");
		}

		$fingerPrint = $this->featureVectorHashService->generateFingerprint(
			$persistedTimeSeries->getNormalizedFeatureVector(),
			$topLevelCategory
		);

		$persistedTimeSeries->setFingerprint($fingerPrint);
	}

	public function __construct(ElasticSearch $elasticSearch, ISiteAttributeRepository $siteAttributeRepository,
		FeatureVectorHashService $featureVectorHashService){

		$this->featureVectorHashService = $featureVectorHashService;
		$this->elasticSearch = $elasticSearch;
		$this->siteAttributeRepository = $siteAttributeRepository;
	}

	/**
	 * @return array
	 */
	public function getSubscribedEvents() {
		return array(
			Events::postPersist,
			Events::postLoad,
			Events::postUpdate,
		);
	}

	public function postUpdate(LifecycleEventArgs $args) {
		$entity = $args->getObject();

		/** This class only handles changes to PersistedTimeSeries instances */
		if ($entity instanceof PersistedTimeSeries) {
			/** @var $entity PersistedTimeSeries */

			$this->updateFingerprints($entity);

			/** We need to save the parts of the time series that need saving into ElasticSearch manually */
			$this->elasticSearch->updateFeatureVectorDocument(
				$entity->getRawFeatureVector(),
				$entity->getNormalizedFeatureVector(),
				$entity->getFingerprint(),
				$entity->getTopLevelCategory(),
                $entity->getCategory(),
				$entity->getTagNames(),
                $entity->getDescription(),
                $entity->getName(),
				$entity->getSource(),
				$entity->getDocumentId(),
				$this->siteAttributeRepository->getCurrentFeatureVectorFamily()->getIndexName()
			);
		}
	}

	public function postPersist(LifecycleEventArgs $args) {
		$entity = $args->getObject();

		/** This class only handles changes to PersistedTimeSeries instances */
		if ($entity instanceof PersistedTimeSeries) {
			/** @var $entity PersistedTimeSeries */

			$this->updateFingerprints($entity);

			/** We need to save the parts of the time series that need saving into ElasticSearch manually */
			$elasticSearchDocumentId = $this->elasticSearch->saveFeatureVectorDocument(
				$entity->getRawFeatureVector(),
				$entity->getNormalizedFeatureVector(),
				$entity->getFingerprint(),
				$entity->getTopLevelCategory(),
                $entity->getCategory(),
				$entity->getTagNames(),
                $entity->getDescription(),
                $entity->getName(),
				$entity->getSource(),
				$entity->getId(),
				$this->siteAttributeRepository->getCurrentFeatureVectorFamily()->getIndexName()
			);

			$entity->setDocumentId($elasticSearchDocumentId);
			$args->getEntityManager()->persist($entity);
			$args->getEntityManager()->flush();
		}
	}

	public function postLoad(LifecycleEventArgs $args){
		$entity = $args->getObject();

		/** This class only handles changes to PersistedTimeSeries instances */
		if ($entity instanceof PersistedTimeSeries) {
			/** @var $entity PersistedTimeSeries */

			/** We need to save the parts of the time series that need saving into ElasticSearch manually */
			$response = $this->elasticSearch->getFeatureVectorDocument(
				$entity->getId(),
				$this->siteAttributeRepository->getCurrentFeatureVectorFamily()->getIndexName()
			);

			$rawFeatureVectorEls = $response["_source"]["rawFeatureVector"];
			$normalizedFeatureVector = $response["_source"]["normalizedFeatureVector"];
			$commonFingerprint = $response["_source"]["commonFingerprint"];
			$categoryFingerprint = $response["_source"]["categoryFingerprint"];
			$elasticSearchDocumentId = $response["_id"];

			$entity->setRawFeatureVector(new FeatureVector($rawFeatureVectorEls));
			$entity->setNormalizedFeatureVector(new FeatureVector($normalizedFeatureVector));

			$entity->setFingerprint(new Fingerprint($commonFingerprint, $categoryFingerprint));

			$entity->setDocumentId($elasticSearchDocumentId);
		}
	}

}