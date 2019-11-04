<?php
namespace PresentationLayer\Operators\AuthenticationRequired;

/**
 * Class EAuthenticationRequiredUnauthorized
 * @package PresentationLayer\Operators\AuthenticationRequired
 */
class EAuthenticationRequiredUnauthorized extends \Exception{

    /**
     *
     */
    public function __construct(){
        parent::__construct("Unauthorized.");
    }

} 