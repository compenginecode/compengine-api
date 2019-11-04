<?php

namespace PresentationLayer\Routing\ExceptionHandler;

use PresentationLayer\Routing\StatusCode\Unauthorized;
use Yam\ExceptionHandler\IExceptionHandler;
use Yam\Route\Response\Response;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class AuthenticationRequired
 * @package PresentationLayer\Routing\ExceptionHandler
 */
class AuthenticationRequired implements IExceptionHandler{

    /** handle
     *
     *  Route handler.
     *
     * @param \Exception $exception
     * @param Response $response
     */
    public function handle(\Exception $exception, Response &$response){
        $response->setStatusCode(new Unauthorized());
        $response->setReturnBody(new JSONBody(["message" => "Unauthorized."]));
    }

}