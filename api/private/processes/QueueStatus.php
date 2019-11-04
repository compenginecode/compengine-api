<?php

require_once "../../source/bootstrap.php";

global $container;

$intervalGranularity = 2;

/** @var \InfrastructureLayer\Queueing\Queue\Queue $queue */
$queue = $container->get("InfrastructureLayer\\Queueing\\Queue\\Queue");

$queue->push(new \InfrastructureLayer\Queueing\Message\Message(
	"DomainLayer\\Common\\Examples\\TestMessageWorker\\TestMessageWorker", [1]));

//$worker = new \InfrastructureLayer\Queueing\Worker\Worker($queue, "MyFirstWorker");
//
//$worker->onProcessRequired(function(\InfrastructureLayer\Queueing\Message\Message $message){
//	echo "Worker is here - I did something!";
//	return FALSE;
//});
//
//$queue->push(new \InfrastructureLayer\Queueing\Message\Message("A", ["B"]));
//$worker->tryAndGetMessage();
