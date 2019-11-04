<?php

namespace DomainLayer\BulkUploadRequestService\Requests;

/**
 * Interface IBulkUploadRequest
 * @package DomainLayer\BulkUploadRequestService\Requests
 */
interface IBulkUploadRequest
{
    /** getFilePath
     *
     *
     *
     * @return array
     */
    public function getFile();

    /** getApprovalToken
     *
     *
     *
     * @return string
     */
    public function getApprovalToken();

    /** getExchangeToken
     *
     *
     *
     * @return string
     */
    public function getExchangeToken();
}
