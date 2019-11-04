<?php

namespace DomainLayer\ORM\Fingerprint;

/**
 * Class Fingerprint
 * @package DomainLayer\ORM\Fingerprint
 */
class Fingerprint {

	/**
	 * @var array
	 */
	protected $commonFingerprint;

	/**
	 * @var array
	 */
	protected $categoryFingerprint;

	/**
	 * Fingerprint constructor.
	 * @param $commonFingerprint
	 * @param $categoryFingerprint
	 */
	public function __construct($commonFingerprint, $categoryFingerprint) {
		$this->commonFingerprint = $commonFingerprint;
		$this->categoryFingerprint = $categoryFingerprint;
	}

	/**
	 * @return mixed
	 */
	public function getCommonFingerprint() {
		return $this->commonFingerprint;
	}

	/**
	 * @return mixed
	 */
	public function getCategoryFingerprint() {
		return $this->categoryFingerprint;
	}

}