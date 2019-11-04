<?php

namespace PresentationLayer\Routing\StatusCode;

use Yam\Route\Response\StatusCode\IStatusCode;

/**
 * Class NotModified
 * @package PresentationLayer\Routing\StatusCode
 */
class TooManyRequests implements IStatusCode{

    /** toStatus
     *
     *  Returns the status code.
     *
     * @return string
     */
    public function toStatus(){
        return "429";
    }

} 