<?php

namespace InfrastructureLayer\Caching\CacheAdaptor\RedisCacheAdaptor;

class ENoActiveHosts extends \Exception{

    public function __construct(){
        return parent::__construct("Could not connect to any Redis host.");
    }
}