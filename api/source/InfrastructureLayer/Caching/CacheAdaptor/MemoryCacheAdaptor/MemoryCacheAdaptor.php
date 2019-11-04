<?php

namespace InfrastructureLayer\Caching\CacheAdaptor\MemoryCacheAdaptor;

use InfrastructureLayer\Caching\CacheAdaptor\ICacheAdaptor;

class MemoryCacheAdaptor implements ICacheAdaptor{

    private $cache = [];

    public function getValue($key){
        if (isset($this->cache[$key])){
            return $this->cache[$key]["value"];
        }
        return null;
    }

    public function setValue($key, $value, $expire){
        $this->cache[$key] = ["value" => $value, "expire" => $expire];
    }

    public function deleteValue($key){
        unset($this->cache[$key]);
    }

}