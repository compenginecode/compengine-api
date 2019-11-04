<?php

namespace InfrastructureLayer\Queueing\Processor;

use DI\Container;
use InfrastructureLayer\Process\HealthCheck\HealthCheckService\HealthCheckService;
use InfrastructureLayer\Queueing\Queue\Queue;
use InfrastructureLayer\Queueing\Worker\Worker;

/**
 * Class Processor
 * @package InfrastructureLayer\Queueing\Processor
 */
class Processor {

	protected $container;

	protected $queue;

	protected $healthCheckService;

	public function __construct(Container $container, Queue $queue, HealthCheckService $healthCheckService){
		$this->container = $container;
		$this->queue = $queue;
		$this->healthCheckService = $healthCheckService;
	}

	public function startProcessing($serviceName){
		echo "Worker started.\n";

		$counter = 0;
		$healthCheckGranularity = 5;

		$this->healthCheckService->registerHealthCheck($serviceName, $healthCheckGranularity);
		while(1){
			$counter = (++$counter % $healthCheckGranularity);
			if (0 === $counter){
				$this->healthCheckService->notifyIAmHealthy();
			}

			$message = $this->queue->popAndTransfer("processing-queue");
			if (FALSE !== $message){
				$id = $message->getId();

				/** We now process the message */
				/** @var Worker $worker */
				$worker = $this->container->get($message->getFullyQualifiedTaskName());
				$processResult = $worker->processMessage($message);

				/** Check how the processing went */
				if (FALSE === $processResult){
					$this->queue->push($message);
					echo "Message $id processing failed. \n";
				}else{
					echo "Message $id processing completed successfully. \n";
				}
				$this->queue->backend()->lrem("processing-queue", 1, $message->serialize());
			}

			sleep(1);
		}
	}

}