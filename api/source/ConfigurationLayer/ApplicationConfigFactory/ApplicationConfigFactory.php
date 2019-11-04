<?php

namespace ConfigurationLayer\ApplicationConfigFactory;

use ConfigurationLayer\ApplicationConfig\ApplicationConfig;
use ConfigurationLayer\ApplicationConfig\EEnvironmentVariableMissing;

class ApplicationConfigFactory{

    public static function createFromFile($filePath, $environment){
        return new ApplicationConfig($filePath, $environment);
    }

    public static function createFromEnvironment($filePath){
        $keyName = "TMD_ROLE";

        $state = getenv($keyName);
        if (empty($state)){
            throw new EEnvironmentVariableMissing($keyName);
        }

        return self::createFromFile($filePath, strtolower($state));
    }

}