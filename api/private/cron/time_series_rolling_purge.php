<?php

require_once "../../source/bootstrap.php";

global $container;
global $configuration;
global $entityManager;

while(TRUE) {

    /** We have to disconnect/reconnect as the MySQL server will time out on us */
    $entityManager->getConnection()->close();
    $entityManager->getConnection()->connect();

    /** @var \DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService\TimeSeriesDownloadService $service */
    $service = $container->get(\DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService\TimeSeriesDownloadService::class);

    $jsonFiles = $service->getDirContents(\DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService\TimeSeriesDownloadService::JSON_STORAGE_DIRECTORY);
    $csvFiles = $service->getDirContents(\DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService\TimeSeriesDownloadService::CSV_STORAGE_DIRECTORY);

    $fiveDaysAgo = new DateTime();
    $fiveDaysAgo->modify("-5 days");

    if (!empty($jsonFiles)) {
        foreach ($jsonFiles as $jsonFile) {
            $fileCreationDate = new DateTime();
            $fileCreationDate->setTimestamp(filectime($jsonFile));

            if ($fileCreationDate <= $fiveDaysAgo) {
                unlink($jsonFile);
            }
        }
    }

    if (!empty($csvFiles)) {
        foreach ($csvFiles as $csvFile) {
            $fileCreationDate = new DateTime();
            $fileCreationDate->setTimestamp(filectime($csvFile));

            if ($fileCreationDate <= $fiveDaysAgo) {
                unlink($csvFile);
            }
        }
    }

    sleep(60*60*24);

}