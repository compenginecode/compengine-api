<?php

namespace DomainLayer\ContributorService;

use DomainLayer\ORM\Contributor\Contributor;

/**
 * Interface IUnsubscribeContributorRequest
 * @package DomainLayer\ContributorService
 */
interface IUnsubscribeContributorRequest
{
    /** getToken
     *
     *
     *
     * @return string
     */
    public function getToken();

    /** getContributor
     *
     *
     *
     * @return Contributor
     */
    public function getContributor();
}
