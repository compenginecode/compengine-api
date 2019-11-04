<?php

namespace PresentationLayer\Routes\Contributors\Id\Unsubscribe\Requests;

use DomainLayer\ContributorService\IUnsubscribeContributorRequest;
use DomainLayer\ORM\Contributor\Contributor;
use PresentationLayer\Routes\EInvalidInputs;

/**
 * Class UnsubscribeContributorWebRequest
 * @package PresentationLayer\Routes\Contributors\Id\Unsubscribe\Requests
 */
class UnsubscribeContributorWebRequest implements IUnsubscribeContributorRequest
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
    public function populate(Contributor $contributor, $webRequest) {
        $this->contributor = $contributor;
        if (!isset($webRequest["token"]) || empty($webRequest["token"])) {
            throw new EInvalidInputs("token is required");
        }
        $this->webRequest = $webRequest;
    }

    /** getToken
     *
     *
     *
     * @return string
     */
    public function getToken() {
        return $this->webRequest["token"];
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
