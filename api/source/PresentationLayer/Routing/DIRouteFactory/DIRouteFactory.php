<?php

namespace PresentationLayer\Routing\DIRouteFactory;

use DI\Container;
use Yam\Router\RouteFactory\IRouteFactory;

class DIRouteFactory implements IRouteFactory{

    private $diContainer;

    public function __construct(Container $container){
        $this->diContainer = $container;
    }

    public function instantiateRoute($routeClassName){
        return $this->diContainer->get($routeClassName);
    }
}