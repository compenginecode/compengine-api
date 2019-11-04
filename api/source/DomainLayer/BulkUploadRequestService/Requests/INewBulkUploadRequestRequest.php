<?php

namespace DomainLayer\BulkUploadRequestService\Requests;

/**
 * Interface INewBulkUploadRequestRequest
 * @package DomainLayer\BulkUploadRequestService\Requests
 */
interface INewBulkUploadRequestRequest
{
    /** getName
     *
     *
     *
     * @return string
     */
    public function getName();

    /** getEmailAddress
     *
     *
     *
     * @return string
     */
    public function getEmailAddress();

    /** getOrganisation
     *
     *
     *
     * @return string
     */
    public function getOrganisation();

    /** getDescription
     *
     *
     *
     * @return string
     */
    public function getDescription();
}
