<?php

namespace PresentationLayer\Routing\StatusCode;

use Yam\Route\Response\StatusCode\IStatusCode;

/**
 * Class NotModified
 * @package PresentationLayer\Routing\StatusCode
 */
class NotModified implements IStatusCode{

    /** toStatus
     *
     *  Returns the status code.
     *
     * @return string
     */
    public function toStatus(){
        return "304";
    }

} 