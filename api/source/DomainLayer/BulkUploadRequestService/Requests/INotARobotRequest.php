<?php

namespace DomainLayer\BulkUploadRequestService\Requests;

/**
 * Interface INotARobotRequest
 * @package DomainLayer\BulkUploadRequestService\Requests
 */
interface INotARobotRequest
{
    /** getRecaptchaResponseCode
     *
     *
     *
     * @return string
     */
    public function getRecaptchaResponseCode();

    /** getApprovalToken
     *
     *
     *
     * @return string
     */
    public function getApprovalToken();
}
