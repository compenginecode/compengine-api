<?php

namespace DomainLayer\Common\Enum;

/**
 * Class Enum
 * @package DomainLayer\Common\Enum
 */
abstract class Enum{

    /** $value
     *
     *  Value of the enumeration.
     *
     * @var bool|float|int|null|string
     */
    protected $value;

    /** __construct
     *
     *  Constructor. Accessible only through static factory methods.
     *
     * @param $value
     */
    protected function __construct($value){
        $this->value = $value;
    }

    /** equals
     *
     *  Returns TRUE if the value matches that supplied, FALSE otherwise.
     *
     * @throws \Exception
     * @param $value
     * @return mixed
     */
    public function equals($value){
        if (!is_string($value)){
            throw new \Exception("Comparison must be against a string literal.");
        }
        return $this->value == $value;
    }

    /** chosenOption
     *
     *  Returns the enum chosen option.
     *
     * @return bool|float|int|null|string
     */
    public function chosenOption(){
        return $this->value;
    }

    /** options
     *
     *  Returns an array of all the options of this enumeration.
     *
     * @return array
     */
    public static function options(){
        $reflection = new \ReflectionClass(get_called_class());
        return $reflection->getConstants();
    }

    /** isValidOption
     *
     *  Returns TRUE if the parameter is a valid option, FALSE otherwise.
     *
     * @param $option
     * @return bool
     */
    public static function isValidOption($option){
        return in_array($option, self::options());
    }

    /** byValue
     *
     *  Creates an enum instance by value.
     *
     * @param $value
     * @return static
     */
    public static function byValue($value){
        return new static($value);
    }

    /** isInvalid
     *
     *  Returns TRUE if the current value is an invalid option, and FALSE othewise.
     *  This can occur through hydration through the ORM.
     *
     * @return bool
     */
    public function isInvalid(){
        return !in_array($this->value, $this->options());
    }
} 