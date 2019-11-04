<?php

namespace InfrastructureLayer\Process\HealthCheck\HealthStatus;

use DomainLayer\Common\Enum\Enum;

/**
 * Class HealthStatus
 * @package InfrastructureLayer\Process\HealthCheck\HealthStatus
 */
class HealthStatus extends Enum{

	const STATUS_DOWN = "down";
	const STATUS_IN_TROUBLE = "in trouble";
	const STATUS_RUNNING = "running";

	/** down
	 *
	 * 	Returns a new instance of self with value self::STATUS_DOWN.
	 *
	 * @return HealthStatus
	 */
	public static function down(){
		return new self(self::STATUS_DOWN);
	}

	/** inTrouble
	 *
	 * 	Returns a new instance of self with value self::STATUS_IN_TROUBLE.
	 *
	 * @return HealthStatus
	 */
	public static function inTrouble(){
		return new self(self::STATUS_IN_TROUBLE);
	}

	/** running
	 *
	 * 	Returns a new instance of self with value self::STATUS_RUNNING.
	 *
	 * @return HealthStatus
	 */
	public static function running(){
		return new self(self::STATUS_RUNNING);
	}

}