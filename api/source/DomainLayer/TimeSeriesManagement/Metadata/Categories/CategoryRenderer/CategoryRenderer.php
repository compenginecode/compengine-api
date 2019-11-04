<?php

namespace DomainLayer\TimeSeriesManagement\Metadata\Categories\CategoryRenderer;

use DomainLayer\ORM\Category\Category;

/**
 * Class CategoryRenderer
 * @package DomainLayer\TimeSeriesManagement\Metadata\Categories\CategoryRenderer
 */
class CategoryRenderer {

	/** renderCategory
	 *
	 * 	Returns a category rendered as an array ready for JSON serialization.
	 *
	 * @param Category $category
	 * @return array
	 */
	public function renderCategory(Category $category){
		return array(
			"id" => $category->getId(),
			"name" => $category->getName(),
			"approvalStatus" => $category->getApprovalStatus()->chosenOption(),
			"load_on_demand" => $category->getChildren()->count() > 0
		);
	}

}