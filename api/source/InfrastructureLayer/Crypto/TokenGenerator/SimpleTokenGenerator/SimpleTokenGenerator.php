<?php

namespace InfrastructureLayer\Crypto\TokenGenerator\SimpleTokenGenerator;

use InfrastructureLayer\Crypto\TokenGenerator\ITokenGenerator;

/**
 * Class SimpleTokenGenerator
 * @package InfrastructureLayer\Crypto\TokenGenerator\SimpleTokenGenerator
 */
class SimpleTokenGenerator implements ITokenGenerator{

    /** generateToken
     *
     *  Returns a token of the given length.
     *
     * @param $length
     * @return string
     */
    public function generateToken($length){
        return substr(md5(rand()), 0, $length);
    }

} 