<?php

use DomainLayer\NotificationService\NotificationService;

require __DIR__ . '/../../source/bootstrap.php';

global $entityManager;

while(TRUE){
    /** We have to disconnect/reconnect as the MySQL server will time out on us */
    $entityManager->getConnection()->close();
    $entityManager->getConnection()->connect();

    /** @var \DI\Container $container */
    /** @var NotificationService $notificationService */
    $notificationService = $container->make(NotificationService::class);
    $notificationService->sendDailyNotifications();

    sleep(60*60*24);
}