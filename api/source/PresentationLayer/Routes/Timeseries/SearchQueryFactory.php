<?php

namespace PresentationLayer\Routes\Timeseries;


use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\ORM\Source\Repository\ISourceRepository;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery\CategoryFilter\CategoryFilter;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery\SearchQuery;
use PresentationLayer\Routes\EInvalidInputs;

class SearchQueryFactory {

	private $siteAttributeRepository;

	private $sourceRepository;

	public function __construct(ISiteAttributeRepository $siteAttributeRepository, ISourceRepository $sourceRepository){
		$this->siteAttributeRepository = $siteAttributeRepository;
		$this->sourceRepository = $sourceRepository;
	}

	public function constructSearchQuery($getParams, $maxNeighbours){
		$category = CategoryFilter::any();
		if (isset($getParams["topLevelCategory"])){
			if (CategoryFilter::isValidOption($getParams["topLevelCategory"])){
				$category = CategoryFilter::byValue($getParams["topLevelCategory"]);
			}else{
				throw new EInvalidInputs("Invalid value for category. Must be one of: " .
					implode(", ", CategoryFilter::options()));
			}
		}

		$tags = [];
		if (isset($getParams["tags"])){
			$tagsArray = explode(";", $getParams["tags"]);
			if (count($tagsArray) > 0){
				$tags = $tagsArray;
			}
		}

		$currentFVF = $this->siteAttributeRepository->getCurrentFeatureVectorFamily();
		if ($category->equals(CategoryFilter::FILTER_SYNTHETIC)){
			$index = $currentFVF->getSyntheticIndex();
		}else if ($category->equals(CategoryFilter::FILTER_REAL)){
			$index = $currentFVF->getRealIndex();
		}else{
			$index = $currentFVF->getCommonIndex();
		}

		$searchQuery = new SearchQuery($index);
		$searchQuery->setDirectNeighbourLimit($maxNeighbours);
		$searchQuery->setCategoryFilter($category);
		$searchQuery->setTags($tags);

		if (isset($getParams["source"])){
			$searchQuery->setSource($this->sourceRepository->findOneByName($getParams["source"]));
		}

		return $searchQuery;
	}

}