<?php

namespace PresentationLayer\Routing\Plugins\SchemaValidationPlugin;

class ESchemaFileMissing extends \Exception{

    public function __construct($filename, $path){
        return parent::__construct("Schema file '$filename' in path '$path' missing.");
    }
}