<?php

namespace InfrastructureLayer\Crypto\TokenGenerator\CryptoTokenGenerator;

use InfrastructureLayer\Crypto\TokenGenerator\ITokenGenerator;
use RandomLib\Factory;
use SecurityLib\Strength;

/**
 * Class CryptoTokenGenerator
 * @package InfrastructureLayer\Crypto\TokenGenerator\SimpleTokenGenerator
 */
class CryptoTokenGenerator implements ITokenGenerator{

    /** Crypto tokens will use characters from the following list */
    CONST STR_TOKEN_SPACE = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    /** $generator
     *
     *  This is a generator supplied through RandomLib\Factory used to generate
     *  cryptographically strong keys.
     *
     * @var \RandomLib\Generator
     */
    private $generator;

    /** __construct
     *
     *  CryptoTokenGenerator constructor.
     *
     * @param Factory $factory
     */
    public function __construct(Factory $factory){
        $this->generator = $factory->getGenerator(new Strength(Strength::MEDIUM));
    }

    /** generateToken
     *
     *  Returns a token of the given length.
     *
     * @param $length
     * @return string
     */
    public function generateToken($length){
        return $this->generator->generateString($length, self::STR_TOKEN_SPACE);
    }

} 