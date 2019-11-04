<?php

namespace DomainLayer\ORM\LSHOptions;

/**
 * Class LSHOptions
 * @package DomainLayer\ORM\LSHOptions
 */
class LSHOptions {

	protected $indexCount;

	protected $hashCount;

	public function __construct($indexCount, $hashCount){
		$this->indexCount = $indexCount;
		$this->hashCount = $hashCount;
	}

	/**
	 * @return mixed
	 */
	public function getHashCount() {
		return $this->hashCount;
	}

	/**
	 * @return mixed
	 */
	public function getIndexCount() {
		return $this->indexCount;
	}

}