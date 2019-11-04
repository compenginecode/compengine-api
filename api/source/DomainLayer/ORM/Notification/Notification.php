<?php

namespace DomainLayer\ORM\Notification;

use DomainLayer\ORM\DomainEntity\DomainEntity;

/**
 * Class Notification
 * @package DomainLayer\ORM\Notification
 */
class Notification extends DomainEntity
{
    /**
     * Notification Types
     */
    const TIME_SERIES_APPROVED = "Time Series Approved";
    const TIME_SERIES_DENIED = "Time Series Denied";
    const NEW_MESSAGE = "New Messages";
    const SIMILAR_UPLOAD = "Similar Uploads";

    /**
     * Frequencies
     */
    const DAILY = "daily";
    const WEEKLY = "weekly";

    /** frequency
     *
     *
     *
     * @var string
     */
    private $frequency;

    /** name
     *
     *
     *
     * @var string
     */
    private $name;

    /** emailAddress
     *
     *
     *
     * @var string
     */
    private $emailAddress;

    /** type
     *
     *
     *
     * @var string
     */
    private $type;

    /** body
     *
     *
     *
     * @var string
     */
    private $body;

    /** unsubscribeLink
     *
     *
     *
     * @var string
     */
    private $unsubscribeLink;

    /** __construct
     *
     *  Constructor
     *
     * @param string $frequency
     * @param string $name
     * @param string $emailAddress
     * @param string $type
     * @param string|array $body
     * @param string $unsubscribeLink
     */
    public function __construct($frequency, $name, $emailAddress, $type, $body, $unsubscribeLink = null) {
        $this->frequency = $frequency;
        $this->name = $name;
        $this->emailAddress = $emailAddress;
        $this->type = $type;
        $this->body = json_encode($body);
        $this->unsubscribeLink = $unsubscribeLink;
    }

    /** Frequency
     *
     *  Returns the
     *
     * @return string
     */
    public function getFrequency() {
        return $this->frequency;
    }

    /** Name
     *
     *  Returns the
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /** EmailAddress
     *
     *  Returns the
     *
     * @return string
     */
    public function getEmailAddress() {
        return $this->emailAddress;
    }

    /** Type
     *
     *  Returns the
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /** Body
     *
     *  Returns the
     *
     * @return string
     */
    public function getBody() {
        return json_decode($this->body, true);
    }

    /** UnsubscribeLink
     *
     *  Returns the
     *
     * @return string
     */
    public function getUnsubscribeLink() {
        return $this->unsubscribeLink;
    }
}
