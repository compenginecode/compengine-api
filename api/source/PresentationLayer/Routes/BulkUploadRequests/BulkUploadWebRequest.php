<?php

namespace PresentationLayer\Routes\BulkUploadRequests;

use DomainLayer\BulkUploadRequestService\Requests\IBulkUploadRequest;
use PresentationLayer\Routes\EInvalidInputs;

/**
 * Class BulkUploadWebRequest
 * @package PresentationLayer\Routes\BulkUploadRequests
 */
class BulkUploadWebRequest implements IBulkUploadRequest
{
    /** webRequest
     *
     *
     *
     * @var array
     */
    private $webRequest;

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
            !isset($_FILES["file"])
        ) {
            Throw new EInvalidInputs("file, approvalToken, and exchangeToken fields are required");
        }

        $this->webRequest = $webRequest;
    }

    public function getFile() {
        return $_FILES["file"];
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
}
