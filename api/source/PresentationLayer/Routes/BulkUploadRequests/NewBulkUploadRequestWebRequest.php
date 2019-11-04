<?php

namespace PresentationLayer\Routes\BulkUploadRequests;

use DomainLayer\BulkUploadRequestService\Requests\INewBulkUploadRequestRequest;
use PresentationLayer\Routes\EInvalidInputs;

/**
 * Class NewBulkUploadRequestWebRequest
 * @package PresentationLayer\Routes\BulkUploadRequests
 */
class NewBulkUploadRequestWebRequest implements INewBulkUploadRequestRequest
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
        if (!isset($webRequest["name"]) ||
            empty($webRequest["name"]) ||
            !isset($webRequest["emailAddress"]) ||
            empty($webRequest["emailAddress"]) ||
            !isset($webRequest["organisation"]) ||
            empty($webRequest["organisation"]) ||
            !isset($webRequest["description"]) ||
            empty($webRequest["description"])
        ) {
            Throw new EInvalidInputs("Name, EmailAddress, Organisation and Description fields are required");
        }

        if (!filter_var($webRequest["emailAddress"], FILTER_VALIDATE_EMAIL)) {
            Throw new EInvalidInputs("EmailAddress must be formatted as an email address");
        }

        $this->webRequest = $webRequest;
    }

    /** getName
     *
     *
     *
     * @return string
     */
    public function getName() {
        return $this->webRequest["name"];
    }

    /** getEmailAddress
     *
     *
     *
     * @return string
     */
    public function getEmailAddress() {
        return $this->webRequest["emailAddress"];
    }

    /** getOrganisation
     *
     *
     *
     * @return string
     */
    public function getOrganisation() {
        return $this->webRequest["organisation"];
    }

    /** getDescription
     *
     *
     *
     * @return string
     */
    public function getDescription() {
        return $this->webRequest["description"];
    }
}
