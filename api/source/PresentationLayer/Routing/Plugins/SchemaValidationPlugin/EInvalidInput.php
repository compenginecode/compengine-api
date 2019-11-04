<?php

namespace PresentationLayer\Routing\Plugins\SchemaValidationPlugin;

class EInvalidInput extends \Exception{

    public function __construct(){
        return parent::__construct("Input to the schema validator plugin must be an array.");
    }
}