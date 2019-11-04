<?php

namespace DomainLayer\ContributorService;

use DomainLayer\ORM\Contributor\Contributor;

/**
 * Interface IContactContributorRequest
 * @package DomainLayer\ContributorService
 */
interface IContactContributorRequest
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

    /** getMessage
     *
     *
     *
     * @return string
     */
    public function getMessage();

    /** getContributor
     *
     *
     *
     * @return Contributor
     */
    public function getContributor();
}
