<?php

namespace ConfigurationLayer\ApplicationConfig;

class EConfigurationMissing extends \Exception{

    public function __construct(){
        return parent::__construct("Configuration file not found.");
    }

}