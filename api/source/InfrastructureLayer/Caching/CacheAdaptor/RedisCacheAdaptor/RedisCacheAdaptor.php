<?php

namespace InfrastructureLayer\Caching\CacheAdaptor\RedisCacheAdaptor;

use ConfigurationLayer\ApplicationConfig\ApplicationConfig;
use InfrastructureLayer\Caching\CacheAdaptor\ICacheAdaptor;

class RedisCacheAdaptor implements ICacheAdaptor{

    /** @var \Predis\Client */
    protected $predis;

    protected $configuration;

    private function tryConnect(array $options){
        try{
            $this->predis = new \Predis\Client($options);
            $this->predis->ping();
            return TRUE;
        }catch(\Exception $e){
            return FALSE;
        }
    }

    public function __construct(ApplicationConfig $configuration){
        $main = array(
            "scheme" => $configuration->get("redis_master_scheme"),
            "host" => $configuration->get("redis_master_host"),
            "port" => $configuration->get("redis_master_port"),
            "database" => $configuration->get("redis_master_database"),
            "timeout" => $configuration->get("redis_master_timeout"),
        );

        $failOver = array(
            "scheme" => $configuration->get("redis_slave_scheme"),
            "host" => $configuration->get("redis_slave_host"),
            "port" => $configuration->get("redis_slave_port"),
            "database" => $configuration->get("redis_slave_database"),
            "timeout" => $configuration->get("redis_slave_timeout"),
        );
        
        $this->configuration = $configuration;
        if (!($this->tryConnect($main))){
            if (!($this->tryConnect($failOver))){
                throw new ENoActiveHosts();
            }
        }

    }

    public function getValue($key){
        return $this->predis->get($key);
    }

    public function setValue($key, $value, $expire){
        $this->predis->set($key, $value);
        $this->predis->expire($key, $expire);
    }

    public function deleteValue($key){
        $this->predis->del(array($key));
    }

}