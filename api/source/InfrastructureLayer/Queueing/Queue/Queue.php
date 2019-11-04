<?php

namespace InfrastructureLayer\Queueing\Queue;

use InfrastructureLayer\Queueing\Message\Message;
use Predis\Client;

/**
 * Class Queue
 * @package InfrastructureLayer\Queueing\Queue
 */
class Queue {

	private $redisClient;

	protected function getPrimaryQueueKey(){
		return "queue-primary";
	}

	public function __construct(Client $redisClient){
		$this->redisClient = $redisClient;
	}

	public function push(Message $message){
		$wrappedMessage = serialize($message);
		$this->redisClient->lpush($this->getPrimaryQueueKey(), $wrappedMessage);
	}

	public function backend(){
		return $this->redisClient;
	}

	/**
	 * @param $queueName
	 * @return Message
	 */
	public function popAndTransfer($queueName){
		$wrappedMessage = $this->redisClient->rpoplpush($this->getPrimaryQueueKey(), $queueName);
		return unserialize($wrappedMessage);
	}

}