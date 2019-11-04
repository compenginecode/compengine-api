<?php

namespace PresentationLayer\Operators\NoCORS;

use Yam\Route\AbstractRoute;
use Yam\Route\Operators\AbstractOperator;
use Yam\Route\Request\Request;
use Yam\Route\Response\Response;

/**
 * Class NoCORS
 * @package PresentationLayer\Operators\NoCORS
 */
class NoCORS extends AbstractOperator{

    /** executeOperator
     *
     *  Throws an exception if unauthorized.
     *
     * @param Request $request
     * @param Response $response
     * @param AbstractRoute $route
     * @param array $annotations
     */
    public function executeOperator(Request $request, Response $response, AbstractRoute $route, array $annotations){
        $response->setHeader("Access-Control-Allow-Origin", "*");
        $response->setHeader("Access-Control-Allow-Headers", "Authorization, X-Requested-With, X-authentication, X-client, Content-Type");
        $response->setHeader("Access-Control-Allow-Methods", "POST, OPTIONS, DELETE, GET, PUT");
    }

} 