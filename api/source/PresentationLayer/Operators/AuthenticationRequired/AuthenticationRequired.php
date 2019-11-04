<?php

namespace PresentationLayer\Operators\AuthenticationRequired;

use Yam\Route\AbstractRoute;
use Yam\Route\Operators\AbstractOperator;
use Yam\Route\Request\ERequestHeaderMissing;
use Yam\Route\Request\Request;
use Yam\Route\Response\Response;

/**
 * Class AuthenticationRequired
 * @package PresentationLayer\Operators\AuthenticationRequired
 */
class AuthenticationRequired extends AbstractOperator{

    /** executeOperator
     *
     *  Throws an exception if unauthorized.
     *
     * @param Request $request
     * @param Response $response
     * @param AbstractRoute $route
     * @param array $annotations
     * @throws EAuthenticationRequiredUnauthorized
     */
    public function executeOperator(Request $request, Response $response, AbstractRoute $route, array $annotations){
        try{
            $token = $request->getAuthorization()->getTokenAsBearer();
            /** Continue with route... */
        }catch(ERequestHeaderMissing $exception){
            throw new EAuthenticationRequiredUnauthorized();
        }
    }

} 