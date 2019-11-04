<?php

namespace InfrastructureLayer\Sessions\SessionService;

use ConfigurationLayer\ApplicationConfig\ApplicationConfig;
use DomainLayer\ORM\Authenticator\Authenticator;
use InfrastructureLayer\Caching\CacheAdaptor\ICacheAdaptor;
use InfrastructureLayer\Crypto\TokenGenerator\ITokenGenerator;
use InfrastructureLayer\Sessions\SessionToken\SessionToken;

/**
 * Class SessionService
 * @package InfrastructureLayer\Sessions\SessionService
 */
class SessionService{

    /** $cache
     *
     *  Cache adaptor interface.
     *
     * @var \InfrastructureLayer\Caching\CacheAdaptor\ICacheAdaptor
     */
    protected $cache;

    /** $tokenGenerator
     *
     *  Token generator inteface.
     *
     * @var \InfrastructureLayer\Crypto\TokenGenerator\ITokenGenerator
     */
    protected $tokenGenerator;

    /** $applicationConfig
     *
     *  Global application configuration.
     *
     * @var \ConfigurationLayer\ApplicationConfig\ApplicationConfig
     */
    protected $applicationConfig;

    /** generateSession
     *
     *  Generates a session, returns a token.
     *
     * @param $data
     * @param $sessionExpireTimeInSeconds
     * @return string
     */
    protected function generateSession($data, $sessionExpireTimeInSeconds){
        $tokenLength = $this->applicationConfig->get("sessionTokenLength");

        $wrappedData = array(
            "data" => $data,
            "sessionExpireTimeInSeconds" => $sessionExpireTimeInSeconds
        );

        $token = $this->tokenGenerator->generateToken($tokenLength);
        $this->cache->setValue($token, json_encode($wrappedData), $sessionExpireTimeInSeconds);

        return $token;
    }

    /**
     *
     * @param ICacheAdaptor $cache
     * @param ITokenGenerator $tokenGenerator
     * @param ApplicationConfig $applicationConfig
     *
     */
    public function __construct(ICacheAdaptor $cache, ITokenGenerator $tokenGenerator, ApplicationConfig $applicationConfig){
        $this->cache = $cache;
        $this->tokenGenerator = $tokenGenerator;
        $this->applicationConfig = $applicationConfig;
    }

    public function destroySession($token){
        $this->cache->deleteValue($token);
    }

    /** getSession
     *
     *  Returns the session data given when the session was made, or NULL if no data, or session token was
     *  invalid or session has since expired.
     *
     * @param $token
     * @return null|mixed
     */
    public function getSession($token){
        $cacheResponse = $this->cache->getValue($token);
        if (NULL === $cacheResponse){
            return NULL;
        }

        /** Unwrap the session data */
        $wrappedData = json_decode($cacheResponse, JSON_OBJECT_AS_ARRAY);
        return $wrappedData["data"];
    }

    /** refireSession
     *
     *  Refires the session. This means that the session expiration counter will reset
     *  back to the original length it started at. For example, if the session was initially created
     *  under a 2h expiration window, calling this method will reset the counter to zero, giving
     *  the session another 2h expiration window. Returns FALSE if the session token is
     *  invalid.
     *
     * @param $token
     * @return bool|int
     */
    public function refireSession($token){
        $cacheResponse = $this->cache->getValue($token);
        if (NULL === $cacheResponse){
            return FALSE;
        }

        /** Unwrap the session data */
        $wrappedData = json_decode($cacheResponse, JSON_OBJECT_AS_ARRAY);
        $sessionTimeInSeconds = (int)$wrappedData["sessionExpireTimeInSeconds"];

        /** Reset the expiration time. */
        $this->cache->setValue($token, $cacheResponse, $sessionTimeInSeconds);

        /** Return the new time interval (ttl) */
        return $sessionTimeInSeconds;
    }

    /** sessionExists
     *
     *  Returns TRUE if a session exists with the given token, and FALSE otherwise.
     *
     * @param $token
     * @return bool
     */
    public function sessionExists($token){
        return (NULL !== $this->cache->getValue($token));
    }


    public function createSession(Authenticator $authenticator){
		$expiryTime = 60*60*24;
        $token = $this->generateSession($authenticator->getId(), $expiryTime);

        return new SessionToken($token, $expiryTime);
    }

}