<?php

namespace DomainLayer\ORM\DataPoints;

use DomainLayer\Common\DomainEntity\DomainEntityAsTrait;

/**
 * Class DataPoints
 * @package DomainLayer\ORM\DataPoints
 */
class DataPoints {

	use DomainEntityAsTrait;

	protected $dataPoints = [];

	public function __construct(array $dataPoints){
		$this->dataPoints = $dataPoints;
	}

	/**
	 * @return array
	 */
	public function getDataPoints() {
		return $this->dataPoints;
	}

}