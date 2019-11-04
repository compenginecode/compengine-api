<?php

namespace DomainLayer\BulkUploadRequestService\Requests;

use Doctrine\Common\Collections\Collection;
use DomainLayer\ORM\Category\Category;

/**
 * Class ISubmitBulkUploadRequest
 * @package DomainLayer\BulkUploadRequestService\Requests
 */
interface ISubmitBulkUploadRequest
{
    /** getApprovalToken
     *
     *
     *
     * @return mixed
     */
    public function getApprovalToken();

    /** getExchangeToken
     *
     *
     *
     * @return mixed
     */
    public function getExchangeToken();

    /** getTimeSeries
     *
     *
     *
     * @return mixed
     */
    public function getTimeSeries();

    /** getAllowContact
     *
     *
     *
     * @return boolean
     */
    public function getAllowContact();

    /** getMetadataCategory
     *
     *
     *
     * @return Category
     */
    public function getMetadataCategory();

    /** getMetadataSamplingRate
     *
     *
     *
     * @return mixed
     */
    public function getMetadataSamplingRate();

    /** getMetadataSamplingUnit
     *
     *
     *
     * @return mixed
     */
    public function getMetadataSamplingUnit();

    /** getMetadataTags
     *
     *
     *
     * @return Collection
     */
    public function getMetadataTags();

    /** hasMetadataRootWord
     *
     *
     *
     * @return mixed
     */
    public function hasMetadataRootWord();

    /** getMetadataRootWord
     *
     *
     *
     * @return mixed
     */
    public function getMetadataRootWord();

	/**
	 * @return boolean
	 */
    public function getWantsAggregationEmail();

}
