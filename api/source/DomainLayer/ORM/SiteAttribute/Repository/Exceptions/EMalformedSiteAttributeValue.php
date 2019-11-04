<?php

namespace DomainLayer\ORM\SiteAttribute\Repository\Exceptions;

/**
 * Class EMalformedSiteAttributeValue
 * @package DomainLayer\ORM\SiteAttribute\Repository\Exceptions
 */
class EMalformedSiteAttributeValue extends \Exception{

	/** __construct
	 *
	 * 	EMalformedSiteAttributeValue constructor.
	 *
	 * @param string $attributeName
	 */
	public function __construct($attributeName, $message = ""){
		parent::__construct("The site attribute '$attributeName' is malformed. $message");
	}

}