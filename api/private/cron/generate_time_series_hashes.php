<?php

require __DIR__ . '/../../source/bootstrap.php';

use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;

global $entityManager;

$timeSeriesRepository = $entityManager->getRepository(PersistedTimeSeries::class);

$timeSeries = $timeSeriesRepository->findAll();

array_walk($timeSeries, function (PersistedTimeSeries $timeSeries) {
    $hash = hash("sha256", implode(",", $timeSeries->getDataPoints()));
    $timeSeries->setHash($hash);
});

$entityManager->flush();
