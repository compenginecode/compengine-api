<?php

require_once "../../../source/bootstrap.php";

global $entityManager;
global $container;

$test = 50;

/** Mock out the normalizer. We don't actually want to normalize the feature vectors against the population just yet... */
$container->set("DomainLayer\\TimeSeriesManagement\\Ingestion\\FeatureVectorGeneration\\FeatureVectorNormalizationService\\Normalizers\\INormalizer",
	\DI\Object("DomainLayer\\TimeSeriesManagement\\Ingestion\\FeatureVectorGeneration\\FeatureVectorNormalizationService\\Normalizers\\MockNormalizer\\MockNormalizer"));

/** @var \DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository\ITimeSeriesRepository $timeSeriesRepository */
$timeSeriesRepository = $container->get("DomainLayer\\ORM\\TimeSeries\\PersistedTimeSeries\\Repository\\ITimeSeriesRepository");

$rankDepth = 5;
$realTopNeighbours = [];

$lock = 0;
foreach($timeSeriesRepository->findAll() as $aCandidateTimeSeries){
	if ($lock === $test){
		break;
	}
	/** @var \DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries $aCandidateTimeSeries */
	/** @var \DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries $aComparisonTimeSeries */

	$localOrdering = [];
	foreach($timeSeriesRepository->findAll() as $aComparisonTimeSeries){
		if ($aCandidateTimeSeries->getId() !== $aComparisonTimeSeries->getId()){
			$distance = $aComparisonTimeSeries->getNormalizedFeatureVector()->toVector()
				->distance($aCandidateTimeSeries->getNormalizedFeatureVector()->toVector());

			$localOrdering[$aComparisonTimeSeries->getId()] = $distance;
		}
	}

	uasort($localOrdering, function($a, $b){
		return $a > $b;
	});


	$topN = array_splice($localOrdering, 0, min(count($localOrdering), $rankDepth));
	$realTopNeighbours[$aCandidateTimeSeries->getId()] = $topN;

	$lock++;
}

/** @var \DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\NearestNeighbourService $nearestNeighbourService */
$nearestNeighbourService = $container->get(DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\NearestNeighbourService::class);
/** @var \DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository $siteRepository */
$siteRepository = $container->get(DomainLayer\ORM\SiteAttribute\Repository\FullSiteAttributeRepository::class);

$lock = 0;
$rankDepth = 50;
$approximateTopNeighbours = [];
foreach($timeSeriesRepository->findAll() as $aCandidateTimeSeries){
	if ($lock === $test){
		break;
	}

	$searchQuery = new \DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery\SearchQuery(
		$siteRepository->getCurrentFeatureVectorFamily()->getCommonIndex()
	);

	$list = $nearestNeighbourService->findNearestNeighbours(
		$aCandidateTimeSeries->getNormalizedFeatureVector(),
		$searchQuery
	);

	$localOrdering = [];
	foreach($list->getNodes() as $aNodeData){
		$id = $aNodeData["id"];
		if (!("root" === strtolower($id) || $id === $aCandidateTimeSeries->getId())) {
			$localOrdering[$aNodeData["id"]] = $aNodeData["similarity"];
		}
	}

	uasort($localOrdering, function($a, $b){
		return $a > $b;
	});


	$topN = array_splice($localOrdering, 0, min(count($localOrdering), $rankDepth));
	$approximateTopNeighbours[$aCandidateTimeSeries->getId()] = $topN;

	$lock++;
}

$scores = [];
$results = [];
foreach($realTopNeighbours as $candidateTimeSeriesId => $realNeighbours){
	$results[$candidateTimeSeriesId] = array(
		"real" => $realNeighbours,
		"approximate" => $approximateTopNeighbours[$candidateTimeSeriesId]
	);

	$realKeys = array_keys($realNeighbours);
	$approxKeys = array_keys($approximateTopNeighbours[$candidateTimeSeriesId]);
	$shared = array_intersect($realKeys, $approxKeys);
	$scores[] = round(count($shared) / count($realKeys) * 100, 2);
	echo "Score: " . round(count($shared) / count($realKeys) * 100, 2) . PHP_EOL;
}

file_put_contents("out.csv", implode("\n", $scores));
echo "Average score: " . round(array_sum($scores) / count($scores), 2);