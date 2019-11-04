<?php

namespace DomainLayer\ORM\ApprovalStatus;

use DomainLayer\Common\Enum\Enum;

/**
 * Class ApprovalStatus
 * @package DomainLayer\ORM\VisibilityStatus
 */
class ApprovalStatus extends Enum{

    /** Enumeration string literals */
    const UNAPPROVED = "unapproved";
    const APPROVED = "approved";

    /** unapproved
     *
     *  Returns an instance of this class with enum value of self::UNAPPROVED.
     *
     * @return ApprovalStatus
     */
    public static function unapproved(){
        return new self(self::UNAPPROVED);
    }

    /** approved
     *
     *  Returns an instance of this class with enum value of self::APPROVED.
     *
     * @return ApprovalStatus
     */
    public static function approved(){
        return new self(self::APPROVED);
    }

}