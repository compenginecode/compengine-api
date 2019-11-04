<?php

namespace InfrastructureLayer\HumanReadableNumbers;

class HumanReadableNumbers
{
    private static $units = [
        "",
        "k",
        " million",
        " billion",
        " trillion",
        " quadrillion",
        " quintillion",
        " sextillion",
        " septillion"
    ];

    public static function numberToHumanReadableString($number, $decimals = 1)
    {
        $exponent = floor(log10($number));

        if ($exponent < 4) {
            return number_format($number);
        }

        $index = 0;
        while($number > 999){
            $number /= 1000;
            $index++;
        }
        return ("".round($number, 1).static::$units[$index]);
    }
}
