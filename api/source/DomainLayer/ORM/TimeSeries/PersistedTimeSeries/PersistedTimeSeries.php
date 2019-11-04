<?php

namespace DomainLayer\ORM\TimeSeries\PersistedTimeSeries;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DomainLayer\Common\DomainEntity\DomainEntityAsTrait;
use DomainLayer\ORM\BulkUploadRequest\BulkUploadRequest;
use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\Contributor\Contributor;
use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\ORM\Fingerprint\Fingerprint;
use DomainLayer\ORM\SamplingInformation\SamplingInformation;
use DomainLayer\ORM\Source\Source;
use DomainLayer\ORM\Tag\Tag;
use DomainLayer\ORM\TimeSeries\ComparableTimeSeries\ComparableTimeSeries;
use DomainLayer\TimeSeriesManagement\Downsampler\LTTBDownsampler;

/**
 * Class PersistedTimeSeries
 * @package DomainLayer\ORM\TimeSeries\PersistedTimeSeries
 */
class PersistedTimeSeries

	extends ComparableTimeSeries {

	const ORIGIN_SEED = 'seed';
	const ORIGIN_INDIVIDUAL_CONTRIBUTION = 'individual-contribution';
	const ORIGIN_BULK_CONTRIBUTION = 'bulk-contribution';

	use DomainEntityAsTrait;

	/**
	 * @var array
	 */
	protected $downSampledDataPoints30 = [];

	/**
	 * @var array
	 */
	protected $downSampledDataPoints1000 = [];

	/** $name
	 *
	 *    The name of the time series.
	 *
	 * @var string
	 */
	protected $name;

	/**	$origin
	 *
	 * 	 The origin of the time series. See class constants for valid values.
	 *
	 * @var string
	 */
	protected $origin;

	/** $slug
	 *
	 *    A unique URI used to access this time series via the
	 *    internet.
	 *
	 * @var string
	 */
	protected $slug;

	/** $description
	 *
	 *    The description of the time series.
	 *
	 * @var string
	 */
	protected $description;

	/** $source
	 *
	 *    The associated source, and NULL if none assigned.
	 *
	 * @var Source|NULL
	 */
	protected $source;

	/** $tags
	 *
	 *    Collection of tags associated to this time series.
	 *
	 * @var Collection
	 */
	protected $tags;

	/** $category
	 *
	 *    The associated category of this time series.
	 *
	 * @var Category
	 */
	protected $category;

	/** $samplingRate
	 *
	 *    The sampling information of the time sieres.
	 *
	 * @var SamplingInformation
	 */
	protected $samplingInformation;

	/** $contributor
	 *
	 *    The contributor of this time series. May be NULL.
	 *
	 * @var Contributor|NULL
	 */
	protected $contributor;

	/** $documentId
	 *
	 *    A manually maintained foreign key to an ElasticSearch
	 *    document in the common index.
	 *
	 * @var string
	 */
	protected $documentId;

	/** $bulkUploadRequest
	 *
	 *    The BulkUploadRequest this time series was uploaded in. May be NULL.
	 *
	 * @var BulkUploadRequest|NULL
	 */
	protected $bulkUploadRequest;

	/** isApproved
	 *
	 *
	 *
	 * @var boolean
	 */
	protected $isApproved = false;

	/**
	 * @var bool
	 */
	protected $isRejected = true;

	/** hash
	 *
	 *
	 *
	 * @var string|null
	 */
	protected $hash = null;

	/**
	 * @param string $name
	 */
	private function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @param string $description
	 */
	private function setDescription($description)
	{
		if (is_string($description)) {
			$this->description = $description;
		} else {
			$this->description = "";
		}
	}

	/**
	 * @param Source|NULL $source
	 */
	private function setSource($source)
	{
		$this->source = $source;
	}

	/**
	 * @param Collection $tags
	 */
	private function setTags($tags)
	{
		$this->tags = $tags;
	}

	/**
	 * @param Category $category
	 */
	private function setCategory($category)
	{
		$this->category = $category;
	}

	/**
	 * @param mixed $samplingInformation
	 */
	private function setSamplingInformation($samplingInformation)
	{
		$this->samplingInformation = $samplingInformation;
	}

	/**
	 * PersistedTimeSeries constructor.
	 * @param array $dataPoints
	 * @param FeatureVector $rawFeatureVector
	 * @param FeatureVector $normalizedFeatureVector
	 * @param Fingerprint $fingerprint
	 * @param $name
	 * @param $description
	 * @param $source
	 * @param $category
	 * @param $samplingInformation
	 * @param array $tags
	 */
	public function __construct(array $dataPoints, FeatureVector $rawFeatureVector,
		FeatureVector $normalizedFeatureVector, Fingerprint $fingerprint, $name, $description, $source,
		$category, $samplingInformation, array $tags)
	{

		parent::__construct($dataPoints, $rawFeatureVector, $normalizedFeatureVector, $fingerprint);

		$this->downSampledDataPoints30 = (new LTTBDownsampler())->downsample($dataPoints, 30);
		$this->downSampledDataPoints1000 = (new LTTBDownsampler())->downsample($dataPoints, 1000);

		$this->setName($name);
		$this->setDescription($description);
		$this->setSource($source);
		$this->setCategory($category);
		$this->setSamplingInformation($samplingInformation);
		$this->isRejected = false;

		$this->tags = new ArrayCollection();
		foreach ($tags as $aTag) {
			/** @var $aTag Tag */
			$this->tags->add($aTag);
		}
	}

	public function reject()
	{
		$this->isRejected = true;
	}

	/**
	 * @return string
	 */
	public function getDocumentId()
	{
		return $this->documentId;
	}

	/**
	 * @param $commonDocumentId
	 */
	public function setDocumentId($commonDocumentId)
	{
		$this->documentId = $commonDocumentId;
	}

	/** setContributor
	 *
	 *    Sets the contributor.
	 *
	 * @param Contributor|NULL $contributor
	 */
	public function setContributor($contributor)
	{
		$this->contributor = $contributor;
	}

	/**
	 * @return Contributor|NULL
	 */
	public function getContributor()
	{
		return $this->contributor;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getSlug()
	{
		return $this->slug;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @return Source|NULL
	 */
	public function getSource()
	{
		return $this->source;
	}

	/**
	 * @return Collection
	 */
	public function getTags()
	{
		return $this->tags;
	}

	public function getTagNames()
	{
		$names = [];
		foreach ($this->getTags() as $aTag) {
			/** @var $aTag Tag */
			$names[] = $aTag->getName();
		}

		return $names;
	}

	/**
	 * @return Category
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * @return Category
	 */
	public function getTopLevelCategory()
	{
		return $this->category->getTopLevelCategory();
	}

	/**
	 * @return SamplingInformation
	 */
	public function getSamplingInformation()
	{
		return $this->samplingInformation;
	}

	/**
	 * @return array
	 */
	public function getDownSampledDataPoints30()
	{
		return $this->downSampledDataPoints30;
	}

	/**
	 * @return array
	 */
	public function getDownSampledDataPoints1000()
	{
		return $this->downSampledDataPoints1000;
	}

	/** Hash
	 *
	 *  Sets the
	 *
	 * @param null|string $hash
	 */
	public function setHash($hash)
	{
		$this->hash = $hash;
	}

	/** Hash
	 *
	 *  Returns the
	 *
	 * @return null|string
	 */
	public function getHash()
	{
		return $this->hash;
	}

	/**
	 * @return bool
	 */
	public function isApproved()
	{
		return $this->isApproved;
	}

	public function isRejected()
	{
		return $this->isRejected;
	}

	/**
	 * @return void
	 */
	public function approve()
	{
		$this->isApproved = true;
	}

	/**
	 * @return string
	 */
	public function getOrigin()
	{
		return $this->origin;
	}

	/**
	 * @param string $origin
	 */
	public function setOrigin($origin)
	{
		$this->origin = $origin;
	}

}