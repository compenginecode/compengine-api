<?php

/** renormalize.php
 *
 * 	This script will run over the existing (currently utilized) feature vector family and recalculate
 * 	all the normalized feature vectors for each time series. Of course, if the normalized feature vectors
 * 	change, so will the assumed nearest neighbours. As such, the LSH hash family for each time series
 * 	must also be recalculated.
 *
 * 	This is useful if the normalized feature vectors are suffering from "drift", or when you're
 * 	initially seeding the project and the distribution statistics have started from zero.
 *
 * @author A. I. Grayson-Widarsito
 * @date August 2016
 *
 */

require_once "../../../source/bootstrap.php";

global $entityManager;
global $container;

/** Link up the INormalizer interface to the ElasticSearch normalizer.
 * We explicitly define it so we don't get a cached association */
$container->set("DomainLayer\\TimeSeriesManagement\\Ingestion\\FeatureVectorGeneration\\FeatureVectorNormalizationService\\Normalizers\\INormalizer",
		DI\link("DomainLayer\\TimeSeriesManagement\\Ingestion\\FeatureVectorGeneration\\FeatureVectorNormalizationService\\Normalizers\\ElasticSearchNormalizer\\ElasticSearchNormalizer"));

/** @var \DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository\ITimeSeriesRepository $timeSeriesRepository */
$timeSeriesRepository = $container->get("DomainLayer\\ORM\\TimeSeries\\PersistedTimeSeries\\Repository\\ITimeSeriesRepository");
/** @var \DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\FeatureVectorNormalizationService $normalizer */
$normalizer = $container->get(DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\FeatureVectorNormalizationService::class);
/** @var \DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\MappingManifestGenerationService\MappingManifestGenerationService $mappingService */
$mappingService = $container->get(DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\MappingManifestGenerationService\MappingManifestGenerationService::class);

$mapping = $mappingService->generateMappingManifestForCurrentFVFamily();
foreach($timeSeriesRepository->findAll() as $aTimeSeries){
	/** @var $aTimeSeries \DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries **/
	$aTimeSeries->setTimestampUpdated(new DateTime());

	echo "Normalizing feature vector for time series " . $aTimeSeries->getId() . "...\n";
	/** @var $aTimeSeries \DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries */
	$normalizedFeatureVector = $normalizer->normalizeFeatureVector($aTimeSeries->getRawFeatureVector(), $mapping);
	$aTimeSeries->setNormalizedFeatureVector($normalizedFeatureVector);

	echo "Recalculating hash family for time series " . $aTimeSeries->getId() . "...\n";
//	$aTimeSeries->setHash(
//		$mapping->getFeatureVectorFamily()->tot()->generateHash($normalizedFeatureVector->toVector())
//	);

	$entityManager->merge($aTimeSeries);
}

echo "Persisting changes...\n";
$entityManager->flush();
echo "Done.";