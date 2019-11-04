<?php

namespace DomainLayer\ORM\Authenticator;

use PresentationLayer\Routes\EInvalidInputs;

/**
 * Class Authenticator
 * @package DomainLayer\Subdomains\ORM\Authenticator
 */
class Authenticator extends \DomainLayer\ORM\DomainEntity\DomainEntity{

    /** $passwordHash
     *
     *  The password hash.
     *
     * @var string
     */
    protected $passwordHash;

    /** $emailAddress
     *
     *  The emailAddress of the authenticator.
     *
     * @var string
     */
    protected $emailAddress;

    /**
     * @param $emailAddress
     */
    public function changeEmailAddress($emailAddress) {
        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            throw new EInvalidInputs("Invalid email address.");
        }
        $this->emailAddress = $emailAddress;
    }

    public function changePassword($newPassword){
        $this->passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
    }

    /**
     * @return string
     */
    public function getEmailAddress() {
        return $this->emailAddress;
    }

    /**
     * @return string
     */
    public function getEmailAddressLowerCase(){
        return strtolower($this->emailAddress);
    }

    /**
     * @param $password
     * @return bool
     */
    public function authenticateAgainst($password){
        return password_verify($password, $this->passwordHash);
    }

}