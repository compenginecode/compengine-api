<?php

namespace DomainLayer\ORM\Metric;

use DomainLayer\Common\DomainEntity\DomainEntityAsTrait;

/**
 * Class Metric
 * @package DomainLayer\ORM\Metric
 */
class Metric {

	use DomainEntityAsTrait;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * Metric constructor.
	 * @param string $name
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}

}