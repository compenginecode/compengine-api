<?php

namespace ConfigurationLayer\ApplicationConfig;

class EEnvironmentVariableMissing extends \Exception{

    public function __construct($envVar){
        return parent::__construct("Environment variable '$envVar' missing.");
    }

}