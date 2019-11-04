<?php

namespace InfrastructureLayer\Crypto\TokenGenerator;

/**
 * Interface ITokenGenerator
 * @package InfrastructureLayer\Crypto\TokenGenerator
 */
interface ITokenGenerator {

    /** generateToken
     *
     *  Returns a token of length $length.
     *
     * @param $length
     * @return string
     */
    public function generateToken($length);

} 