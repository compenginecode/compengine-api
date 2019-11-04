<?php

namespace PresentationLayer\Routes\BulkUploadRequests;

use DomainLayer\BulkUploadRequestService\Requests\INotARobotRequest;
use PresentationLayer\Routes\EInvalidInputs;

/**
 * Class NotARobotWebRequest
 * @package PresentationLayer\Routes\BulkUploadRequests
 */
class NotARobotWebRequest implements INotARobotRequest
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
        if (!isset($webRequest["recaptchaResponseCode"]) ||
            empty($webRequest["recaptchaResponseCode"]) ||
            !isset($webRequest["approvalToken"]) ||
            empty($webRequest["approvalToken"])
        ) {
            Throw new EInvalidInputs("recaptchaResponseCode and approvalToken are required");
        }

        $this->webRequest = $webRequest;
    }

    /** getRecaptchaResponseCode
     *
     *
     *
     * @return string
     */
    public function getRecaptchaResponseCode() {
        return $this->webRequest["recaptchaResponseCode"];
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
}
