<?php

use DomainLayer\TimeSeriesManagement\Ingestion\BulkContributionService\BulkContributionService;

require __DIR__ . '/../../source/bootstrap.php';

global $entityManager;

/** @var \DI\Container $container */
/** @var BulkContributionService $bulkContributionService */
$bulkContributionService = $container->make(BulkContributionService::class);

while(1) {

    /** We have to disconnect/reconnect as the MySQL server will time out on us */
    $entityManager->getConnection()->close();
    $entityManager->getConnection()->connect();

    echo "Running contribution service.\n";
    $bulkContributionService->run();  // contribute approved time series
    echo "Done contribution + clean up for now. Sleeping for 30secs...zzz\n";
    sleep(30);
}