<?php

namespace PresentationLayer\Routing\ReturnBody;

use Yam\Route\Response\ReturnBody\IReturnBody;

class ErrorBody implements IReturnBody{

    private $errorType;
    private $message;

    public function __construct($errorType, $message){
        $this->errorType = $errorType;
        $this->message = $message;
    }

    public function getAsResponseBody(){
        return json_encode(array(
            "response" => $this->errorType,
            "message" => $this->message
        ), JSON_PRETTY_PRINT);
    }

} 