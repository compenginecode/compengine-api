<?php

require_once "../../source/bootstrap.php";

global $container;
global $configuration;
global $entityManager;

/** This gets triggered as soon as system is restarted.
 *  Need to wait 1 min to ensure elastic search is booted. */
sleep(60*1);

while(TRUE) {

    /** We have to disconnect/reconnect as the MySQL server will time out on us */
    $entityManager->getConnection()->close();
    $entityManager->getConnection()->connect();

    /** @var \DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService\TimeSeriesDownloadService $service */
    $service = $container->get(\DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService\TimeSeriesDownloadService::class);

    echo "Starting CSV generation.\n";
    $service->generateTimeSeriesAsCsv();

    echo "Starting JSON generation.\n";
    $service->generateTimeSeriesAsJson();

    echo "Done.\n";
    sleep(60*60*24);
}
