<?php

require_once "../../source/bootstrap.php";

global $container;
global $redis;

/** Start the queue delegator. It will start working on messages that are queued */
$processor = $container->get(InfrastructureLayer\Queueing\Processor\Processor::class);
$processor->startProcessing("queue-delegator-a");
