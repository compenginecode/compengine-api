<?php

namespace DomainLayer\Common\Vector;

/**
 * Class Vector
 * @package DomainLayer\Common\Vector
 */
class Vector implements \IteratorAggregate, \ArrayAccess {

    private $data = [];

    public static function fromArray($array){
        return new self($array);
    }

    public static function unserialize($string){
        return new self(unserialize($string));
    }

    public function __construct($data){
        $this->data = array_values($data);
    }

    public function getIterator(){
        return new \ArrayIterator($this->data);
    }

    public function offsetSet($offset,$value) {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset) {
        if ($this->offsetExists($offset)) {
            unset($this->data[$offset]);
        }
    }

    public function offsetGet($offset) {
        return $this->offsetExists($offset) ? $this->data[$offset] : null;
    }

    public function dimension(){
        return count($this->data);
    }

    public function magnitude(){
        $total = 0;
        for($i = 0; $i < $this->dimension(); $i++){
            $total += pow($this[$i], 2);
        }

        return sqrt($total);
    }

    public function distance(Vector $v){
        $total = 0;
        for($i = 0; $i < $this->dimension(); $i++){
            $total += pow($this[$i] - $v[$i], 2);
        }

        return $total;
    }

    public function __toString(){
        return implode(",", $this->data);
    }

    public function dot(Vector $vector){
        if ($this->dimension() === $vector->dimension()){
            $total = 0;

            for($i = 0; $i < $this->dimension(); $i++){
                $total += $this[$i] * $vector[$i];
            }

            return $total;
        }else{
            throw new \Exception("Vectors must have same dimension.");
        }
    }

    public function serialize(){
        return serialize($this->data);
    }

} 