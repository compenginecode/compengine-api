<?php

namespace DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery\CategoryFilter;

use DomainLayer\Common\Enum\Enum;

/**
 * Class CategoryFilter
 * @package DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery\CategoryFilter
 */
class CategoryFilter extends Enum{

	/** String enum literals */
	const FILTER_REAL = "real";
	const FILTER_SYNTHETIC = "synthetic";
	const FILTER_ANY = "any";

	/** real
	 *
	 * 	Returns a new instance of self with enum value self::FILTER_REAL.
	 *
	 * @return CategoryFilter
	 */
	public static function real(){
		return new self(self::FILTER_REAL);
	}

	/** synthetic
	 *
	 * 	Returns a new instance of self with enum value self::FILTER_SYNTHETIC.
	 *
	 * @return CategoryFilter
	 */
	public static function synthetic(){
		return new self(self::FILTER_SYNTHETIC);
	}

	/** any
	 *
	 * 	Returns a new instance of self with enum value self::FILTER_ANY.
	 *
	 * @return CategoryFilter
	 */
	public static function any(){
		return new self(self::FILTER_ANY);
	}

}