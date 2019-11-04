<?php

namespace PresentationLayer\Routes\Compare\ComparisonKey;

use PresentationLayer\Routes\TransactionRoute;
use Yam\Route\AbstractRoute;

/**
 * Class AbstractComparisonKeyInferredRoute
 * @package PresentationLayer\Routes\Compare\ComparisonKey
 */
class AbstractComparisonKeyInferredRoute extends TransactionRoute{

	/** $comparisonKey
	 *
	 * 	The comparison key of the time series conversion
	 * 	process.
	 *
	 * @var string
	 */
	protected $comparisonKey;

	/** execute
	 *
	 * 	Called when the route is hit. Must set a response
	 * 	object value.
	 *
	 */
	public function execute(){
		$this->comparisonKey = $this->queryParams[0];
	}

}