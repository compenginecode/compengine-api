<?php

namespace PresentationLayer\Routes\Contributors\Id\Contact\Post\Requests;

use DomainLayer\ContributorService\IContactContributorRequest;
use DomainLayer\ORM\Contributor\Contributor;
use PresentationLayer\Routes\EInvalidInputs;

/**
 * Class ContactContributorWebRequest
 * @package PresentationLayer\Routes\Contributors\Id\Contact\Post
 */
class ContactContributorWebRequest implements IContactContributorRequest
{
    /** webRequest
     *
     *
     *
     * @var array
     */
    private $webRequest;

    /** contributor
     *
     *
     *
     * @var Contributor
     */
    private $contributor;

    /** populate
     *
     *
     *
     * @param Contributor $contributor
     * @param array $webRequest
     * @throws EInvalidInputs
     */
    public function populate($contributor, $webRequest) {
        $this->contributor = $contributor;

        if (!isset($webRequest["name"]) ||
            empty($webRequest["name"]) ||
            !isset($webRequest["emailAddress"]) ||
            empty($webRequest["emailAddress"]) ||
            !isset($webRequest["message"]) ||
            empty($webRequest["message"])
        ) {
            throw new EInvalidInputs("name, emailAddress and message are required");
        }

        if (!filter_var($webRequest["emailAddress"], FILTER_VALIDATE_EMAIL)) {
            Throw new EInvalidInputs("emailAddress is invalid");
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

    /** getMessage
     *
     *
     *
     * @return string
     */
    public function getMessage() {
        return $this->webRequest["message"];
    }

    /** getContributor
     *
     *
     *
     * @return Contributor
     */
    public function getContributor() {
        return $this->contributor;
    }
}
