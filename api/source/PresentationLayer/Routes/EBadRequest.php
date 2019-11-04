<?php

namespace PresentationLayer\Routes;

/**
 * Class EBadRequest
 * @package PresentationLayer\Routes
 */
class EBadRequest extends \Exception {

    /** __construct
     *
     *  Constructor.
     *
     * @param string $message
     */
    public function __construct($message){
        parent::__construct($message);
    }

}