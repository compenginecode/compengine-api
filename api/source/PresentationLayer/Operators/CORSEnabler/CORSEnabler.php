<?php

namespace PresentationLayer\Operators\CORSEnabler;

use Yam\Route\AbstractRoute;
use Yam\Route\Operators\AbstractOperator;
use Yam\Route\Request\Request;
use Yam\Route\Response\Response;

/**
 * Class CORSEnabler
 * @package PresentationLayer\Operators\CORSEnabler
 */
class CORSEnabler extends AbstractOperator{

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

        /** At present, Yam does not have an Operator factory interface */
        /** @var $configuration \ConfigurationLayer\ApplicationConfig\ApplicationConfig */
        global $configuration;

        /** If we are allowed to share resources with the host, then show the Access-Control-Allow-Origin headers */
        $validHosts = array_filter(explode(";", $configuration->get("cross_origin_resource_sharing_hosts")));
        $origin = $request->getHeader("Origin");

        if (in_array($origin, $validHosts) || in_array("*", $validHosts)){
            $host = (in_array("*", $validHosts)) ? "*" : $origin;

            $response->setHeader("Access-Control-Allow-Origin", $host);
            $response->setHeader("Access-Control-Allow-Headers", "Authorization, X-Requested-With, X-authentication, X-client, Content-Type");
            $response->setHeader("Access-Control-Allow-Methods", "POST, OPTIONS, DELETE, GET, PUT");
        }
    }

} 