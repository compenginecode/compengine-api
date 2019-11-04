<?php

require_once "../../../source/bootstrap.php";

global $entityManager;
global $container;

/** Mock out the normalizer. We don't actually want to normalize the feature vectors against the population just yet... */
$container->set("DomainLayer\\TimeSeriesManagement\\Ingestion\\FeatureVectorGeneration\\FeatureVectorNormalizationService\\Normalizers\\INormalizer",
	\DI\Object("DomainLayer\\TimeSeriesManagement\\Ingestion\\FeatureVectorGeneration\\FeatureVectorNormalizationService\\Normalizers\\MockNormalizer\\MockNormalizer"));

if (!isset($argv[1])){
	die("Please specify the ingestion path. Only DAT files are loaded");
}

$ingestionPath = $argv[1];

function logMessage($message){
	echo "$message \n";
}

/** @var \DomainLayer\ORM\Source\Repository\ISourceRepository $sourceRepository */
$sourceRepository = $container->get("DomainLayer\\ORM\\Source\\Repository\\ISourceRepository");
/** @var \DomainLayer\ORM\Category\Repository\ICategoryRepository $categoryRepository */
$categoryRepository = $container->get("DomainLayer\\ORM\\Category\\Repository\\ICategoryRepository");
/** @var \DomainLayer\ORM\Tag\Repository\ITagRepository $tagRepository */
$tagRepository = $container->get("DomainLayer\\ORM\\Tag\\Repository\\ITagRepository");

/** We need to bulk ingest files, convert them into time series, and then persist them */
/** @var \DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester\TimeSeriesIngester $ingester */
$ingester = $container->get(\DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester\TimeSeriesIngester::class);
foreach(glob($ingestionPath . "/*.*") as $aFile){
	logMessage("Processing $aFile.");
	$ingestedTimeSeries = $ingester->ingestFile($aFile, true);

	$timeSeries = new \DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries(
		$ingestedTimeSeries->getDataPoints(),
		$ingestedTimeSeries->getRawFeatureVector(),
		$ingestedTimeSeries->getNormalizedFeatureVector(),
		$ingestedTimeSeries->getFingerprint(),
		pathinfo($aFile, PATHINFO_FILENAME),
		$aFile,
		$sourceRepository->getRandomSource(),
		$categoryRepository->getRandomCategory(),
		\DomainLayer\ORM\SamplingInformation\SamplingInformation::notDefined(),
		$tagRepository->getRandomArrayOfTags(3)
	);

	$timeSeries->setOrigin(DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries::ORIGIN_SEED);

	$entityManager->persist($timeSeries);
	$entityManager->flush();
}
