<?php

namespace PresentationLayer\Routing\StatusCode;

use Yam\Route\Response\StatusCode\IStatusCode;

/**
 * Class UnprocessableEntity
 * @package PresentationLayer\Routing\StatusCode
 */
class UnprocessableEntity implements IStatusCode{

    /** toStatus
     *
     *  Returns the status code.
     *
     * @return string
     */
    public function toStatus(){
        return "422";
    }

} 