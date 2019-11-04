<?php

namespace PresentationLayer\Routing\StatusCode;

use Yam\Route\Response\StatusCode\IStatusCode;

/**
 * Class Conflict
 * @package PresentationLayer\Routing\StatusCode
 */
class Conflict implements IStatusCode{

    /** toStatus
     *
     *  Returns the status code.
     *
     * @return string
     */
    public function toStatus(){
        return "409";
    }

} 