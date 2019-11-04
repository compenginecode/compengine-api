<?php

namespace PresentationLayer\Routing\StatusCode;

use Yam\Route\Response\StatusCode\IStatusCode;

/**
 * Class Unauthorized
 * @package PresentationLayer\Routing\StatusCode
 */
class Unauthorized implements IStatusCode{

    /** toStatus
     *
     *  Returns the status code.
     *
     * @return string
     */
    public function toStatus(){
        return "401";
    }

} 