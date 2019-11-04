<?php

namespace PresentationLayer\Routing\ExceptionHandler;

use PresentationLayer\Routing\StatusCode\Unauthorized;
use Yam\ExceptionHandler\IExceptionHandler;
use Yam\Route\Response\Response;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;
use Yam\Route\Response\StatusCode\StatusForbidden;

/**
 * Class InvalidPermissions
 * @package PresentationLayer\Routing\ExceptionHandler
 */
class InvalidPermissions implements IExceptionHandler{

    /** handle
     *
     *  Route handler.
     *
     * @param \Exception $exception
     * @param Response $response
     */
    public function handle(\Exception $exception, Response &$response){
        $response->setStatusCode(new StatusForbidden());
        $response->setReturnBody(new JSONBody(["message" => "Your privileges do not allow this action."]));
    }

}