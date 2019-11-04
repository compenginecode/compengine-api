<?php

namespace PresentationLayer\Routes\BulkUploadRequests;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DomainLayer\ORM\Category\Category;
use DomainLayer\BulkUploadRequestService\Requests\ISubmitBulkUploadRequest;
use DomainLayer\ORM\Category\Repository\ICategoryRepository;
use DomainLayer\ORM\Tag\Repository\ITagRepository;
use PresentationLayer\Routes\EInvalidInputs;

/**
 * Class SubmitBulkUploadRequest
 * @package PresentationLayer\Routes\BulkUploadRequests
 */
class SubmitBulkUploadRequest implements ISubmitBulkUploadRequest
{
    /** webRequest
     *
     *
     *
     * @var array
     */
    private $webRequest;

    /** categoryRepository
     *
     *
     *
     * @var ICategoryRepository
     */
    private $categoryRepository;

    /** tagRepository
     *
     *
     *
     * @var ITagRepository
     */
    private $tagRepository;

    /** __construct
     *
     *  Constructor
     *
     * @param ICategoryRepository $categoryRepository
     * @param ITagRepository $tagRepository
     */
    public function __construct(ICategoryRepository $categoryRepository, ITagRepository $tagRepository) {
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
    }

    /** fill
     *
     *
     *
     * @param array $webRequest
     * @throws EInvalidInputs
     */
    public function fill($webRequest) {
        if (!isset($webRequest["approvalToken"]) ||
            empty($webRequest["approvalToken"]) ||
            !isset($webRequest["exchangeToken"]) ||
            empty($webRequest["exchangeToken"]) ||
            !isset($webRequest["allowContact"]) ||
            !isset($webRequest["timeSeries"]) ||
            !is_array($webRequest["timeSeries"]) ||
            empty($webRequest["timeSeries"]) ||
            !isset($webRequest["metadata"]["category"]) ||
            empty($webRequest["metadata"]["category"]) ||
            !isset($webRequest["metadata"]["samplingRate"]) ||
            empty($webRequest["metadata"]["samplingRate"]) ||
            !isset($webRequest["metadata"]["samplingUnit"]) ||
            empty($webRequest["metadata"]["samplingUnit"]) ||
            !isset($webRequest["metadata"]["tags"]) ||
            empty($webRequest["metadata"]["tags"]) ||
            !is_array($webRequest["metadata"]["tags"])
        ) {
            Throw new EInvalidInputs("timeSeries(array), approvalToken, exchangeToken, allowContact, metadata.category, metadata.samplingRate, metadata.samplingUnit, metadata.tags(array) fields are required");
        }

        $this->checkMetadataCategory($webRequest);

        $this->webRequest = $webRequest;
    }

    /** getApprovalToken
     *
     *
     *
     * @return string
     */
    public function getApprovalToken() {
        return $this->webRequest["approvalToken"];
    }

    /** getExchangeToken
     *
     *
     *
     * @return string
     */
    public function getExchangeToken() {
        return $this->webRequest["exchangeToken"];
    }

    /** getTimeSeries
     *
     *
     *
     * @return mixed
     */
    public function getTimeSeries() {
        return $this->webRequest["timeSeries"];
    }

    /** getAllowContact
     *
     *
     *
     * @return bool
     */
    public function getAllowContact() {
        return !! $this->webRequest["allowContact"];
    }

    /** checkMetadataCategory
     *
     *
     *
     * @param $webRequest
     * @throws EInvalidInputs
     */
    public function checkMetadataCategory($webRequest) {
        if (! $this->categoryRepository->findById($webRequest["metadata"]["category"])) {
            Throw new EInvalidInputs("categories used do not exist.");
        }
    }

    /** getCategory
     *
     * 	Returns the category associated to the time series.
     *
     * @throws EInvalidInputs
     * @return Category
     */
    public function getMetadataCategory() {
        return $this->categoryRepository->findById($this->webRequest["metadata"]["category"]);
    }

    /** getTags
     *
     * 	Returns all the tags associated with the time series.
     *
     * @return Collection
     * @throws EInvalidInputs
     */
    public function getMetadataTags() {
        $assignedTags = [];
        foreach($this->webRequest["metadata"]["tags"] as $aTagName){
            $assignedTags[] = $this->tagRepository->findByNameOrCreate($aTagName);
        }

        return new ArrayCollection($assignedTags);
    }

    /** getMetadataSamplingRate
     *
     *
     *
     * @return mixed
     */
    public function getMetadataSamplingRate() {
        return $this->webRequest["metadata"]["samplingRate"];
    }

    /** getMetadataSamplingUnit
     *
     *
     *
     * @return mixed
     */
    public function getMetadataSamplingUnit() {
        return $this->webRequest["metadata"]["samplingUnit"];
    }

    /** hasMetadataRootWord
     *
     *
     *
     * @return bool
     */
    public function hasMetadataRootWord() {
        return isset($this->webRequest["metadata"]["rootWord"]);
    }

    /** getMetadataRootWord
     *
     *
     *
     * @return mixed
     */
    public function getMetadataRootWord() {
        return $this->webRequest["metadata"]["rootWord"];
    }

	/**
	 * @return boolean
	 */
	public function getWantsAggregationEmail(){
		return (bool)$this->webRequest['wantsAggregationEmail'];
	}

}
