<?php

namespace DomainLayer\ORM\SiteAttribute\Repository\Exceptions;

/**
 * Class ESiteAttributeMissing
 * @package DomainLayer\ORM\SiteAttribute\Repository\Exceptions
 */
class ESiteAttributeMissing extends \Exception{

	/** __construct
	 *
	 * 	ESiteAttributeMissing constructor.
	 *
	 * @param string $attributeName
	 */
	public function __construct($attributeName){
		parent::__construct("The site attribute '$attributeName' was not found.");
	}

}