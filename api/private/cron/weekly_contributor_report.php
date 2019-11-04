<?php

require __DIR__ . '/../../source/bootstrap.php';

global $entityManager;

while (true) {

	/** We fire this task between the given periods */
	$currentTime = new \DateTime();
	$start = \DateTime::createFromFormat('h:i a', '5:00 pm');
	$end = \DateTime::createFromFormat('h:i a', '5:06 pm');

	if ($currentTime >= $start && $currentTime <= $end && 'Sat' === (new DateTime())->format('D')) {
		echo "Sending off weekly contributor report at " . $currentTime->format('H:i a') . ".\n";

		$entityManager->getConnection()->close();
		$entityManager->getConnection()->connect();

		/** @var \DI\Container $container */
		/** @var \DomainLayer\ContributorSimilarTimeSeriesService\ContributorSimilarTimeSeriesService $report */
		$report = $container->make(\DomainLayer\ContributorSimilarTimeSeriesService\ContributorSimilarTimeSeriesService::class);
		$report->sendOutEmails(new DateTime(), function($text){
			echo $text . PHP_EOL;
		});

	} else {
		echo "Current time is " . $currentTime->format('H:i a') . ', so not sending off weekly report.' . PHP_EOL;
	}

	sleep(300);
}