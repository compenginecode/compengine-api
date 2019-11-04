<?php

namespace PresentationLayer\Routes;

/**
 * Class EInvalidInputs
 * @package PresentationLayer\Routes
 */
class EInvalidInputs extends \Exception{

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
