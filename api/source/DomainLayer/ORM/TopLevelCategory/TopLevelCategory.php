<?php

namespace DomainLayer\ORM\TopLevelCategory;
use DomainLayer\Common\Enum\Enum;

/**
 * Class TopLevelCategory
 * @package DomainLayer\ORM\TopLevelCategory
 */
class TopLevelCategory extends Enum{

	const CATEGORY_UNKNOWN = "unknown";
	const CATEGORY_REAL = "real";
	const CATEGORY_SYNTHETIC = "synthetic";

	public static function unknown(){
		return new self(self::CATEGORY_UNKNOWN);
	}

	public static function real(){
		return new self(self::CATEGORY_REAL);
	}

	public static function synthetic(){
		return new self(self::CATEGORY_SYNTHETIC);
	}

}