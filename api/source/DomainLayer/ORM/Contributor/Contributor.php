<?php

namespace DomainLayer\ORM\Contributor;

use DomainLayer\ORM\DomainEntity\DomainEntity;

/**
 * Class Contributor
 * @package DomainLayer\ORM\Source
 */
class Contributor extends DomainEntity{

    /** $name
     *
     *  The full name of the contributor.
     *
     * @var string
     */
    protected $name;

    /** $emailAddress
     *
     *  The email address of the contributor.
     *
     * @var string
     */
    protected $emailAddress;

    /** $wantsAggregationEmail
     *
     *  TRUE when the contributor wants an aggregation email, and FALSE otherwise.
     *
     * @var bool
     */
    protected $wantsAggregationEmail;

    /** unsubscribeToken
     *
     *
     *
     * @var string
     */
    protected $unsubscribeToken;

    /** __construct
     *
     *  Contributor constructor.
     *
     * @param $name
     * @param $emailAddress
     */
    public function __construct($name, $emailAddress){
        $this->name = $name;
        $this->emailAddress = $emailAddress;
        $this->wantsAggregationEmail = FALSE;
    }

    /** setWantsAggregationEmail
     *
     *  Sets whether the client wants an aggregation email or not.
     *
     * @param $bool
     */
    public function setWantsAggregationEmail($bool){
        $this->wantsAggregationEmail = $bool;
    }

    /** getName
     *
     *  Returns the name of the contributor.
     * 
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    /** getEmailAddress
     *
     *  Returns the email address of the contributor.
     *
     * @return string
     */
    public function getEmailAddress() {
        return $this->emailAddress;
    }

    /** wantsAggregationEmail
     *
     *  Returns TRUE if the contributor wants an aggregation email, and FALSE otherwise.
     *
     * @return boolean
     */
    public function wantsAggregationEmail() {
        return $this->wantsAggregationEmail;
    }

    /** UnsubscribeToken
     *
     *  Returns the
     *
     * @return string
     */
    public function getUnsubscribeToken() {
        return $this->unsubscribeToken;
    }

    /** UnsubscribeToken
     *
     *  Sets the
     *
     * @param string $unsubscribeToken
     */
    public function setUnsubscribeToken($unsubscribeToken) {
        $this->unsubscribeToken = $unsubscribeToken;
    }

}