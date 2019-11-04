<?php

namespace DomainLayer\Common\Examples\TestMessageWorker;

use Doctrine\ORM\EntityManager;
use InfrastructureLayer\Queueing\Message\Message;
use InfrastructureLayer\Queueing\Worker\Worker;

/**
 * Class TestMessageWorker
 * @package DomainLayer\Common\Examples\TestMessageWorker
 */
class TestMessageWorker extends Worker{

	private $entityManager;

	public function __construct(EntityManager $entityManager){
		$this->entityManager = $entityManager;
	}

	public function processMessage(Message $message){
		return TRUE;
	}
}