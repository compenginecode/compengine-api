<?php

namespace DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery;

use DomainLayer\ORM\FeatureVectorIndex\FeatureVectorIndex;
use DomainLayer\ORM\Source\Source;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery\CategoryFilter\CategoryFilter;

class SearchQuery {

	private $categoryFilter;

	private $directNeighbourLimit;

	private $featureVectorIndex;

	private $tags;

	private $source = NULL;

	public function __construct(FeatureVectorIndex $featureVectorIndex, $directNeighbourLimit = 20){
		$this->featureVectorIndex = $featureVectorIndex;

		$this->categoryFilter = CategoryFilter::any();
		$this->directNeighbourLimit = $directNeighbourLimit;
		$this->tags = [];
	}

	/**
	 * @param CategoryFilter $categoryFilter
	 */
	public function setCategoryFilter($categoryFilter) {
		$this->categoryFilter = $categoryFilter;
	}

	/**
	 * @return CategoryFilter
	 */
	public function getCategoryFilter() {
		return $this->categoryFilter;
	}

	/**
	 * @return mixed
	 */
	public function getDirectNeighbourLimit() {
		return $this->directNeighbourLimit;
	}

	/**
	 * @param mixed $directNeighbourLimit
	 */
	public function setDirectNeighbourLimit($directNeighbourLimit) {
		$this->directNeighbourLimit = $directNeighbourLimit;
	}

	public function toHash(){
		return $this->categoryFilter->chosenOption();
	}

	/**
	 * @return FeatureVectorIndex
	 */
	public function getFeatureVectorIndex() {
		return $this->featureVectorIndex;
	}

	public function setTags(array $tagNames){
		$this->tags = $tagNames;
	}

	/**
	 * @return array
	 */
	public function getTags(){
		return $this->tags;
	}

	/**
	 * @return Source|NULL
	 */
	public function getSource() {
		return $this->source;
	}

	/**
	 * @param Source|NULL $source
	 */
	public function setSource($source) {
		$this->source = $source;
	}

}