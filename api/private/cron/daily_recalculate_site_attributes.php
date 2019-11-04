<?php

require_once "../../source/bootstrap.php";

global $container;
global $configuration;
global $entityManager;

/** This gets triggered as soon as system is restarted.
 *  Need to wait 1 min to ensure elastic search is booted. */

while(TRUE) {

    /** We have to disconnect/reconnect as the MySQL server will time out on us */
    $entityManager->getConnection()->close();
    $entityManager->getConnection()->connect();

    $dataPointCount = $entityManager->getRepository(\DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries::class)->createQueryBuilder('ts')
        ->select("SUM(LENGTH(d.dataPoints) - LENGTH(REPLACE(d.dataPoints, 'i', '')))")
        ->leftJoin("ts.dataPoints", "d")->getQuery()->getSingleScalarResult();
    echo "Found $dataPointCount data points.\n";

    /** @var \DomainLayer\ORM\SiteAttribute\SiteAttribute $attribute */
    $attribute = $entityManager->getRepository(\DomainLayer\ORM\SiteAttribute\SiteAttribute::class)->findOneBy(['key' => 'totalDataPoints']);

    if (! $attribute) {
        $attribute = new DomainLayer\ORM\SiteAttribute\SiteAttribute('totalDataPoints', $dataPointCount);
        $entityManager->persist($attribute);
    } else {
        $attribute->setValue($dataPointCount);
    }

    $entityManager->flush();

    echo "Snoozing.\n";
    sleep(60*60*24);
}
