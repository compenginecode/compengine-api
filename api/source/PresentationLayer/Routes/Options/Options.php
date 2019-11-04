<?php

namespace PresentationLayer\Routes\Options;

use ConfigurationLayer\ApplicationConfig\ApplicationConfig;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Options
 * @package PresentationLayer\Routes\Options\Options
 */
class Options extends AbstractRoute{

    /**
     * @var \ConfigurationLayer\ApplicationConfig\ApplicationConfig
     */
    private $applicationConfig;

    /**
     * @param ApplicationConfig $applicationConfig
     */
    public function __construct(ApplicationConfig $applicationConfig){
        $this->applicationConfig = $applicationConfig;
    }

    /** execute
     *
     *  OPTIONS response controller for CORS support.
     *
     */
    public function execute(){
        /** If we are allowed to share resources with the host, then show the Access-Control-Allow-Origin headers */
        $validHosts = array_filter(explode(";", $this->applicationConfig->get("cross_origin_resource_sharing_hosts")));
        $origin = $this->request->getHeader("Origin");

        if (in_array($origin, $validHosts) || in_array("*", $validHosts)){
            $host = (in_array("*", $validHosts)) ? "*" : $origin;
            $this->response->setHeader("Access-Control-Allow-Origin", $host);
        }

        $this->response->setHeader("Access-Control-Allow-Headers", "Authorization, X-Requested-With, X-authentication, X-client, Content-Type, Cache-Control");
        $this->response->setHeader("Access-Control-Allow-Methods", "POST, OPTIONS, DELETE, GET, PUT, PATCH");
        $this->response->setReturnBody(new JSONBody([]));
    }
}