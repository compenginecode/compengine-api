<?php

namespace PresentationLayer\Routes\Timeseries\Post\Requests;

use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\Category\Repository\ICategoryRepository;
use DomainLayer\ORM\Contributor\Contributor;
use DomainLayer\ORM\Contributor\Repository\IContributorRepository;
use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\ORM\FeatureVectorDocument\FeatureVectorDocument;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\ORM\SamplingInformation\SamplingInformation;
use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\ORM\Source\Repository\ISourceRepository;
use DomainLayer\ORM\Source\Source;
use DomainLayer\ORM\Tag\Repository\ITagRepository;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonStore\ComparisonStore;
use DomainLayer\TimeSeriesManagement\Ingestion\ContributionService\Requests\IContributeTimeSeriesRequest;
use PresentationLayer\Routes\EInvalidInputs;

/**
 * Class ContributeTimeSeriesWebRequest
 * @package PresentationLayer\Routes\Timeseries\Put\Requests
 */
class ContributeTimeSeriesWebRequest implements IContributeTimeSeriesRequest{

	/** $webRequest
	 *
	 * 	The raw web request array. It's not clean, nor trusted.
	 *
	 * @var array
	 */
	private $webRequest;

	/** $populated
	 *
	 * 	TRUE if the web request and client have been passed into this object, and FALSE otherwise.
	 *
	 * @var bool
	 */
	private $populated = FALSE;

	/**
	 * @var ComparisonStore
	 */
	private $comparisonStore;

	/**
	 * @var ISourceRepository
	 */
	private $sourceRepository;

	/**
	 * @var ICategoryRepository
	 */
	private $categoryRepository;

	/**
	 * @var ITagRepository
	 */
	private $tagRepository;

	/**
	 * @var IContributorRepository
	 */
	private $contributorRepository;

	/**
	 * @var ISiteAttributeRepository
	 */
	private $siteAttributeRepository;

	/** ensurePopulated
	 *
	 * 	Ensures that the $populated attribute is truthy. Throws exception if not.
	 *
	 * @throws \Exception
	 */
	protected function ensurePopulated(){
		if (!$this->populated){
			throw new \Exception("Web request not populated. Please call populateRequest() method first.");
		}
	}

	/** ensureKeyPresent
	 *
	 * 	Throws an EInvalidInputs exception if $key is not a valid key set on the
	 * 	$webRequest array.
	 *
	 * @param $key
	 * @param $messageOverride
	 * @throws EInvalidInputs
	 */
	protected function ensureKeyPresent($key, $messageOverride = NULL){
		if (!isset($this->webRequest[$key])){
			if (NULL !== $messageOverride){
				throw new EInvalidInputs($messageOverride);
			}else{
				throw new EInvalidInputs("The '$key' attribute is required.");
			}
		}
	}

	public function __construct(ComparisonStore $comparisonStore, ISourceRepository $sourceRepository,
		ICategoryRepository $categoryRepository, ITagRepository $tagRepository,
		IContributorRepository $contributorRepository, ISiteAttributeRepository $siteAttributeRepository){

		$this->comparisonStore = $comparisonStore;
		$this->sourceRepository = $sourceRepository;
		$this->categoryRepository = $categoryRepository;
		$this->tagRepository = $tagRepository;
		$this->contributorRepository = $contributorRepository;
		$this->siteAttributeRepository = $siteAttributeRepository;
	}

	/** populateRequest
	 *
	 * 	Sets up the web request for use.
	 *
	 * @param array $webRequest
	 */
	public function populateRequest(array $webRequest){
		$this->webRequest = $webRequest;
		$this->populated = TRUE;
	}

	/**	getName
	 *
	 * 	Returns the name of the time series.
	 *
	 * @return string
	 */
	public function getName(){
		$this->ensurePopulated();
		$this->ensureKeyPresent("name");

		return $this->webRequest["name"];
	}

	/**	getDescription
	 *
	 * 	Returns the description of the time series.
	 *
	 * @return string
	 */
	public function getDescription(){
		$this->ensurePopulated();
		$this->ensureKeyPresent("description");

		return $this->webRequest["description"];
	}

	/** getSource
	 *
	 * 	Returns the source associated to the time series. Returns NULL if none
	 * 	is supplied.
	 *
	 * @return Source|NULL
	 */
	public function getSource(){
		$this->ensurePopulated();
		$this->ensureKeyPresent("source");

		/** We don't want to save an enpty entity */
		if (empty($this->webRequest["source"])){
			return NULL;
		}

		return $this->sourceRepository->findByNameOrCreate($this->webRequest["source"]);
	}


	/** getSamplingInformation
	 *
	 * 	Returns the sampling information associated to the time series.
	 *
	 * @return SamplingInformation
	 */
	public function getSamplingInformation(){
		$this->ensurePopulated();
		$this->ensureKeyPresent("samplingRate");
		$this->ensureKeyPresent("samplingUnit");

		if (empty($this->webRequest["samplingRate"]) && empty($this->webRequest["samplingUnit"])){
			return SamplingInformation::notDefined();
		}else{
			return SamplingInformation::defined($this->webRequest["samplingRate"], $this->webRequest["samplingUnit"]);
		}
	}

	/** getCategory
	 *
	 * 	Returns the category associated to the time series.
	 *
	 * @throws EInvalidInputs
	 * @return Category
	 */
	public function getCategory(){
		$this->ensurePopulated();
		$this->ensureKeyPresent("categoryId");

		$selectedCategory = $this->categoryRepository->findById($this->webRequest["categoryId"]);
		if (NULL === $selectedCategory){
			throw new EInvalidInputs("Invalid category ID '" . $this->webRequest["categoryId"] . "'.");
		}

		/** If we have a suggested category name present, we're actually making a new category */
		if (isset($this->webRequest["suggestedCategoryName"])){
			$suggestedCategory = $selectedCategory->addChild($this->webRequest["suggestedCategoryName"]);
			return $suggestedCategory;
		}

		return $selectedCategory;
	}

	/** getTags
	 *
	 * 	Returns all the tags sassociated with the time series.
	 *
	 * @return array
	 */
	public function getTags(){
		$this->ensurePopulated();
		$this->ensureKeyPresent("tags");

		$assignedTags = [];
		foreach($this->webRequest["tags"] as $aTagName){
			$assignedTags[] = $this->tagRepository->findByNameOrCreate($aTagName);
		}

		return $assignedTags;
	}

	/**	getDataPoints
	 *
	 * 	Returns an array of data points.
	 *
	 * @return array
	 */
	public function getDataPoints(){
		$this->ensurePopulated();
		$this->ensureKeyPresent("resultKey");

		//todo: remove the string literal coupling on the end!
		return $this->comparisonStore->retrieveComparisonResult(
			$this->webRequest["resultKey"])["timeSeries"]->getDataPoints();
	}

	/**	getRawFeatureVector
	 *
	 * 	Returns the raw feature vector.
	 *
	 * @return FeatureVector
	 */
	public function getRawFeatureVector(){
		$this->ensurePopulated();
		$this->ensureKeyPresent("resultKey");

		//todo: remove the string literal coupling on the end!
		return $this->comparisonStore->retrieveComparisonResult(
			$this->webRequest["resultKey"])["timeSeries"]->getRawFeatureVector();
	}

	/**	getNormalizedFeatureVector
	 *
	 * 	Returns the normalized feature vector.
	 *
	 * @return FeatureVector
	 */
	public function getNormalizedFeatureVector(){
		$this->ensurePopulated();
		$this->ensureKeyPresent("resultKey");

		//todo: remove the string literal coupling on the end!
		return $this->comparisonStore->retrieveComparisonResult(
			$this->webRequest["resultKey"])["timeSeries"]->getNormalizedFeatureVector();
	}

	/**	getHashFamily
	 *
	 * 	Returns the array of hashes.
	 *
	 * @return array
	 */
	public function getHashFamily(){
		$this->ensurePopulated();
		$this->ensureKeyPresent("resultKey");

		//todo: remove the string literal coupling on the end!
		return $this->comparisonStore->retrieveComparisonResult(
			$this->webRequest["resultKey"])["timeSeries"]->getHashFamily();
	}

	/** getFeatureVectorFamily
	 *
	 * 	Returns the feature vector family to contribute the time series to.
	 *
	 * @return FeatureVectorFamily
	 */
	public function getFeatureVectorFamily(){
		return $this->siteAttributeRepository->getCurrentFeatureVectorFamily();
	}

	/**	getContributor
	 *
	 * 	Returns the contributor, if present.
	 *
	 * @return Contributor|NULL
	 */
	public function getContributor(){
		$this->ensurePopulated();

		if (isset($this->webRequest["contactPermissionGiven"]) && TRUE === $this->webRequest["contactPermissionGiven"]){
			$this->ensureKeyPresent("contributorName", "When contact permission is given, the 'contributorName' key must be defined.");
			$this->ensureKeyPresent("contributorName", "When contact permission is given, the 'contributorName' key must be defined.");
			$this->ensureKeyPresent("aggregationPermissionGiven", "When contact permission is given, the 'aggregationPermissionGiven' key must be defined.");

			/** Lookup if we already have this contributor. If not, we create one. */
			$contributor = $this->contributorRepository->findByEmailAddress($this->webRequest["contributorEmailAddress"]);
			if (NULL === $contributor){
				$contributor = new Contributor($this->webRequest["contributorName"], $this->webRequest["contributorEmailAddress"]);
			}

			/** We set this no matter what */
			$contributor->setWantsAggregationEmail((bool)$this->webRequest["aggregationPermissionGiven"]);
			return $contributor;
		}

		return NULL;
	}

}