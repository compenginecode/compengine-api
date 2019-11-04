<?php

namespace DomainLayer\ORM\SiteAttribute;

use DomainLayer\ORM\DomainEntity\DomainEntity;

/**
 * Class SiteAttribute
 * @package DomainLayer\ORM\SiteAttribute
 */
class SiteAttribute extends DomainEntity{

	/** $key
	 *
	 * 	The key, or name of the site property. This is
	 * 	a unique value.
	 *
	 * @var string
	 */
	protected $key;

	/** $value
	 *
	 * 	The value linked to the key.
	 *
	 * @var string
	 */
	protected $value;

	/** __construct
	 *
	 * 	SiteAttribute constructor.
	 *
	 * @param $key
	 * @param $value
	 */
	public function __construct($key, $value){
		$this->key = $key;
		$this->value = (string)$value;
	}

	/** getValue
	 *
	 * 	Returns the value.
	 *
	 * @return string
	 */
	public function getValue() {
		return $this->value;
	}

	/** getKey
	 *
	 * 	Returns the key.
	 *
	 * @return string
	 */
	public function getKey() {
		return $this->key;
	}

    /**
     * @param $value
     */
	public function setValue($value) {
	    $this->value = $value;
    }

}