<?php

namespace DomainLayer\ORM\EmailAddress;

/**
 * Class EmailAddress
 * @package DomainLayer\ORM\EmailAddress
 */
class EmailAddress
{
    /** emailAddress
     *
     *  The email address
     *
     * @var string
     */
    private $emailAddress;

    /** __construct
     *
     *  Constructor
     *
     * @param string $emailAddress
     */
    public function __construct($emailAddress) {
        $this->emailAddress = $emailAddress;
    }

    /** emailAddress
     *
     *  Returns the email address as a string.
     *
     * @return string
     */
    public function emailAddress() {
        return $this->emailAddress;
    }
}