<?php

namespace InfrastructureLayer\Caching\CacheAdaptor;

interface ICacheAdaptor{

    public function getValue($key);

    public function setValue($key, $value, $expire);

    public function deleteValue($key);

}