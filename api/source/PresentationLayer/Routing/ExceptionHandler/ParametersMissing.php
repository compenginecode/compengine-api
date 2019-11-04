<?php

namespace PresentationLayer\Routing\ExceptionHandler;

use PresentationLayer\Routing\StatusCode\Unauthorized;
use PresentationLayer\Routing\StatusCode\UnprocessableEntity;
use Yam\ExceptionHandler\IExceptionHandler;
use Yam\Route\Response\Response;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class ParametersMissing
 * @package PresentationLayer\Routing\ExceptionHandler
 */
class ParametersMissing implements IExceptionHandler{

    /** handle
     *
     *  Route handler.
     *
     * @param \Exception $exception
     * @param Response $response
     */
    public function handle(\Exception $exception, Response &$response){
        $message = $exception->getMessage();

        /** We want to make one particular message more useful. */
        if ("Property [root] must be one of the following types: [object]" === $message){
            $message = "Please sent data along with request, or ensure that data has been sent in raw POST or PUT format. If you did send data, make sure it's valid JSON.";
        }

        $response->setStatusCode(new UnprocessableEntity());
        $response->setReturnBody(new JSONBody(["message" => $message]));
    }

}