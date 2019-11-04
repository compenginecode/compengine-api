<?php

namespace InfrastructureLayer\Queueing\Worker;

use InfrastructureLayer\Queueing\Message\Message;

abstract class Worker {

	abstract public function processMessage(Message $message);

}