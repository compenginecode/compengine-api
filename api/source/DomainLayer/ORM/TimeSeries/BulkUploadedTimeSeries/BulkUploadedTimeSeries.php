<?php

namespace DomainLayer\ORM\TimeSeries\BulkUploadedTimeSeries;

use Doctrine\Common\Collections\Collection;
use DomainLayer\Common\DomainEntity\DomainEntityAsTrait;
use DomainLayer\ORM\BulkUploadRequest\BulkUploadRequest;
use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\Contributor\Contributor;
use DomainLayer\ORM\SamplingInformation\SamplingInformation;
use DomainLayer\ORM\Source\Source;
use DomainLayer\ORM\Tag\Tag;
use DomainLayer\ORM\TimeSeries\RawTimeSeries\RawTimeSeries;

/**
 * Class BulkUploadedTimeSeries
 * @package DomainLayer\ORM\TimeSeries\BulkUploadedTimeSeries
 */
class BulkUploadedTimeSeries extends RawTimeSeries
{
    use DomainEntityAsTrait;

    /** $name
     *
     * 	The name of the time series.
     *
     * @var string
     */
    protected $name;

    /** $slug
     *
     * 	A unique URI used to access this time series via the
     * 	internet.
     *
     * @var string
     */
    protected $slug;

    /** $description
     *
     * 	The description of the time series.
     *
     * @var string
     */
    protected $description;

    /** $source
     *
     * 	The associated source, and NULL if none assigned.
     *
     * @var Source|NULL
     */
    protected $source;

    /** $tags
     *
     * 	Collection of tags associated to this time series.
     *
     * @var Collection
     */
    protected $tags;

    /** $category
     *
     * 	The associated category of this time series.
     *
     * @var Category
     */
    protected $category;

    /** $samplingRate
     *
     * 	The sampling information of the time sieres.
     *
     * @var SamplingInformation
     */
    protected $samplingInformation;

    /** $contributor
     *
     * 	The contributor of this time series. May be NULL.
     *
     * @var Contributor|NULL
     */
    protected $contributor;

    /** $documentId
     *
     * 	A manually maintained foreign key to an ElasticSearch
     * 	document in the common index.
     *
     * @var string
     */
    protected $documentId;

    /** $bulkUploadRequest
     *
     * 	The BulkUploadRequest this time series was uploaded in. May be NULL.
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

    /** isDenied
     *
     *
     *
     * @var boolean
     */
    protected $isDenied = false;

    /** isApproved
     *
     *
     *
     * @var boolean
     */
    protected $isSubmitted = false;

	/**
	 * @var bool
	 */
    protected $isProcessed = false;

    /** fileSize
     *
     *
     *
     * @var int
     */
    protected $fileSize;

    public function __construct($dataPoints, $fileSize) {
        parent::__construct($dataPoints);
        $this->setSamplingInformation(SamplingInformation::notDefined());
        $this->fileSize = $fileSize;
    }

    /** BulkUploadRequest
     *
     *  Returns the
     *
     * @return BulkUploadRequest|NULL
     */
    public function getBulkUploadRequest() {
        return $this->bulkUploadRequest;
    }

    /** BulkUploadRequest
     *
     *  Sets the
     *
     * @param BulkUploadRequest|NULL $bulkUploadRequest
     */
    public function setBulkUploadRequest($bulkUploadRequest) {
        $this->bulkUploadRequest = $bulkUploadRequest;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @param string $description
     */
    private function setDescription($description){
        if (is_string($description)) {
            $this->description = $description;
        }else{
            $this->description = "";
        }
    }

    /**
     * @param Source|NULL $source
     */
    private function setSource($source) {
        $this->source = $source;
    }

    /**
     * @param Collection $tags
     */
    public function setTags($tags) {
        $this->tags = $tags;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category) {
        $this->category = $category;
    }

    /**
     * @param mixed $samplingInformation
     */
    public function setSamplingInformation($samplingInformation) {
        $this->samplingInformation = $samplingInformation;
    }

    /** setContributor
     *
     * 	Sets the contributor.
     *
     * @param Contributor|NULL $contributor
     */
    public function setContributor($contributor){
        $this->contributor = $contributor;
    }

    /**
     * @return Contributor|NULL
     */
    public function getContributor() {
        return $this->contributor;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSlug() {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @return Source|NULL
     */
    public function getSource() {
        return $this->source;
    }

    /**
     * @return Collection
     */
    public function getTags() {
        return $this->tags;
    }

    /** getTagNames
     *
     *
     *
     * @return array
     */
    public function getTagNames(){
        $names = [];
        foreach($this->getTags() as $aTag){
            /** @var $aTag Tag */
            $names[] = $aTag->getName();
        }

        return $names;
    }

    /**
     * @return Category
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * @return Category
     */
    public function getTopLevelCategory(){
        return $this->category->getTopLevelCategory();
    }

    /**
     * @return SamplingInformation
     */
    public function getSamplingInformation() {
        return $this->samplingInformation;
    }

    /** setAsApproved
     *
     *
     *
     */
    public function setAsApproved() {
        $this->isDenied = false;
        $this->isApproved = true;
    }

    /** IsApproved
     *
     *  Returns the
     *
     * @return bool
     */
    public function isApproved() {
        return $this->isApproved;
    }

    /** IsDenied
     *
     *  Returns the
     *
     * @return bool
     */
    public function isDenied() {
        return $this->isDenied;
    }

    /** setAsDenied
     *
     *
     *
     */
    public function setAsDenied() {
        $this->isApproved = false;
        $this->isDenied = true;
    }

    /** isSubmitted
     *
     *
     *
     * @return bool
     */
    public function isSubmitted() {
        return $this->isSubmitted;
    }

    /** setAsSubmitted
     *
     *
     *
     */
    public function setAsSubmitted() {
        $this->isSubmitted = true;
    }

    /** isPendingApproval
     *
     *
     *
     * @return bool
     */
    public function isPendingApproval() {
        return $this->isSubmitted() && !$this->isDenied() && !$this->isApproved();
    }

	/**
	 * @return bool
	 */
	public function isProcessed()
	{
		return $this->isProcessed;
	}

	/**
	 * @param bool $isProcessed
	 */
	public function setIsProcessed($isProcessed)
	{
		$this->isProcessed = $isProcessed;
	}

}
