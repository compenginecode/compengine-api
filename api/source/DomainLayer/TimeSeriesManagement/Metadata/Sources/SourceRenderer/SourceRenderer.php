<?php

namespace DomainLayer\TimeSeriesManagement\Metadata\Sources\SourceRenderer;

use DomainLayer\ORM\Source\Source;

/**
 * Class SourceRenderer
 * @package DomainLayer\TimeSeriesManagement\Metadata\Sources\SourceRenderer
 */
class SourceRenderer {

	/** renderSource
	 *
	 * 	Returns a source rendered as an array ready for JSON serialization.
	 *
	 * @param Source $source
	 * @return array
	 */
	public function renderSource(Source $source){
		return array(
			"id" => $source->getId(),
			"name" => $source->getName()
		);
	}

}