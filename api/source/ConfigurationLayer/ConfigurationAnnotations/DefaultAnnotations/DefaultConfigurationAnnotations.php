<?php

namespace ConfigurationLayer\ConfigurationAnnotations\DefaultAnnotations;

use ConfigurationLayer\ConfigurationAnnotations\IConfigurationAnnotations;

class DefaultConfigurationAnnotations implements IConfigurationAnnotations{

    private $rootDirectory = NULL;

    private function checkAnnotations(){
        if (empty($this->rootDirectory)){
            throw new EAnnotationEmpty("rootDirectory");
        }
    }

    public function parseString($string){
        $this->checkAnnotations();

        $keys = ["%ROOT%"];
        $values = [$this->rootDirectory];
        return str_replace($keys, $values, $string);
    }

    public function setRootDirectory($directory){
        $this->rootDirectory = rtrim($directory, "\\") . "\\";
    }

}