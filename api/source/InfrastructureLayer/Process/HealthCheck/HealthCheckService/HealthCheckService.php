<?php

namespace InfrastructureLayer\Process\HealthCheck\HealthCheckService;

use InfrastructureLayer\Caching\CacheAdaptor\ICacheAdaptor;
use InfrastructureLayer\Process\HealthCheck\HealthCheckService\Exceptions\ERegistrationRequired;
use InfrastructureLayer\Process\HealthCheck\HealthStatus\HealthStatus;

/**
 * Class HealthCheckService
 * @package InfrastructureLayer\Process\HealthCheck\HealthCheckService
 */
class HealthCheckService {

	/** $alias
	 *
	 * 	This is a unique name used to mark the results of a
	 * 	health check.
	 *
	 * @var string
	 */
	protected $alias;

	/** $desiredUpdateInterval
	 *
	 * 	This is the interval in which the host of the health check
	 * 	is opting to try and inform us of their status. We use this
	 * 	to determine if they're simply late, or actually, truly down.
	 *
	 * @var int
	 */
	protected $desiredUpdateInterval;

	/** $cacheAdaptor
	 *
	 * 	Interface to a cache.
	 *
	 * @var ICacheAdaptor
	 */
	private $cacheAdaptor;

	/** formKey
	 *
	 * 	Converts a local key to a globally unique, collision free key.
	 *
	 * @param $key
	 * @return string
	 */
	protected function formKey($key){
		return "health-check-service-$key";
	}

	/** __construct
	 *
	 * 	HealthCheckService constructor.
	 *
	 * @param ICacheAdaptor $cacheAdaptor
	 */
	public function __construct(ICacheAdaptor $cacheAdaptor){
		$this->cacheAdaptor = $cacheAdaptor;
	}

	/** registerHealthCheck
	 *
	 * 	Registers a health check through the given alias and desired update interval.
	 *
	 * @param $alias
	 * @param $desiredUpdateInterval
	 */
	public function registerHealthCheck($alias, $desiredUpdateInterval){
		$this->alias = $alias;
		$this->desiredUpdateInterval = $desiredUpdateInterval;

		$this->notifyIAmHealthy();
	}

	/** notifyIAmHealthy
	 *
	 * 	Marks a status of healthy against the given alias registered.
	 *
	 * @throws ERegistrationRequired
	 */
	public function notifyIAmHealthy(){
		if (!isset($this->alias)){
			throw new ERegistrationRequired();
		}

		$key = $this->formKey($this->alias);
		$healthPayload = array(
			"desiredUpdateInterval" => $this->desiredUpdateInterval,
			"lastCheck" => time()
		);

		$this->cacheAdaptor->setValue($key, json_encode($healthPayload), 24*60*60);
	}

	/** getHealthStatus
	 *
	 * 	Returns the health status of the given registered health check alias. An alias
	 * 	is "running" if it's reported it within the last two update intervals. If the alias
	 * 	has not returned a check within the last two update intervals, but less than the last
	 *  5 we mark it as "in trouble". Otherwise it is marked as "down".
	 * 
	 * @param $alias
	 * @return HealthStatus
	 */
	public function getHealthStatus($alias){
		$key = $this->formKey($alias);
		$resultString = $this->cacheAdaptor->getValue($key);

		if (NULL !== $resultString){
			$resultObj = json_decode($resultString, JSON_OBJECT_AS_ARRAY);
			$lastCheck = (int)$resultObj["lastCheck"];
			$desiredUpdateInterval = (int)$resultObj["desiredUpdateInterval"];

			$error = (time() - $lastCheck);
			if ($error < 2*$desiredUpdateInterval){
				return HealthStatus::running();
			}

			else if ($error > 2*$desiredUpdateInterval && $error < 5*$desiredUpdateInterval){
				return HealthStatus::inTrouble();
			}
		}

		return HealthStatus::down();
	}

}