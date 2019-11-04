<?php

namespace InfrastructureLayer\Sessions\SessionToken;

/**
 * Class SessionToken
 * @package InfrastructureLayer\Sessions\SessionToken
 */
class SessionToken{

    /** $token
     *
     *  The session token.
     *
     * @var string
     */
    public $token;

    /** $expiresIn
     *
     *  The seconds that the session is valid.
     *
     * @var int (seconds)
     */
    public $expiresIn;

    /** __construct
     *
     *  Constructor.
     *
     * @param $token
     * @param $expiresIn
     */
    public function __construct($token, $expiresIn){
        $this->token = $token;
        $this->expiresIn = $expiresIn;
    }

    /** expiryInSeconds
     *
     *  Returns the expiry time of the session in seconds.
     *
     * @return int
     */
    public function expiryInSeconds(){
        return floor($this->expiresIn);
    }

} 