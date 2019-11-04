<?php

namespace InfrastructureLayer\Process\HealthCheck\HealthCheckService\Exceptions;

/**
 * Class ERegistrationRequired
 * @package InfrastructureLayer\Process\HealthCheck\HealthCheckService\Exceptions
 */
class ERegistrationRequired extends \Exception{

	/** __construct
	 *
	 * 	ERegistrationRequired constructor.
	 *
	 */
	public function __construct(){
		parent::__construct("Please call registerHealthCheck first before registering a healthy status.");
	}

}