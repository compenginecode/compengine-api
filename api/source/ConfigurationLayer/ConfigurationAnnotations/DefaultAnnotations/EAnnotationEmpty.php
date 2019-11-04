<?php

namespace ConfigurationLayer\ConfigurationAnnotations\DefaultAnnotations;

class EAnnotationEmpty extends \Exception{

    public function __construct($annotationName){
        return parent::__construct("Configuration annotation '$annotationName' is not defined. Please define it.");
    }

}