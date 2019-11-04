<?php

namespace DomainLayer\TimeSeriesManagement\Metadata\Tags\TagRenderer;

use DomainLayer\ORM\Tag\Tag;

/**
 * Class TagRenderer
 * @package DomainLayer\TimeSeriesManagement\Metadata\Tags\TagRenderer
 */
class TagRenderer {

	/** renderTag
	 *
	 * 	Returns a tag rendered as an array ready for JSON serialization.
	 *
	 * @param Tag $tag
	 * @return array
	 */
	public function renderTag(Tag $tag){
		return array(
			"id" => $tag->getId(),
			"name" => $tag->getName()
		);
	}

}