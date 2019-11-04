<?php

namespace PresentationLayer\Routing\ReturnBody;

use Yam\Route\Response\ReturnBody\IReturnBody;

class CSVBody implements IReturnBody{

    private $content;

    public function __construct($content){
        $this->content = $content;
    }

    public function getAsResponseBody(){
        return $this->content;
    }

} 