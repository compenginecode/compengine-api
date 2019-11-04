<?php

require_once "../source/bootstrap.php";

use \PresentationLayer\Routing\DIRouteFactory\DIRouteFactory;

date_default_timezone_set("UTC");

/** @var \Yam\Router\Router\Router  $router */
$router = $container->get("Yam\\Router\\Router\\Router");

$routeFactory = new DIRouteFactory($container);
$router->setRouteFactory($routeFactory);

$router->registerOperator(new \PresentationLayer\Operators\AuthenticationRequired\AuthenticationRequired(), "Authorization");
$router->registerOperator(new \PresentationLayer\Operators\CORSEnabler\CORSEnabler(), "CORS");

/** Catch all exceptions raised by authentication failures */
$router->registerExceptionHandler(new \PresentationLayer\Routing\ExceptionHandler\AuthenticationRequired(),
    "PresentationLayer\\Operators\\AuthenticationRequired\\EAuthenticationRequiredUnauthorized");

$router->registerExceptionHandler(new \PresentationLayer\Routing\ExceptionHandler\InvalidPermissions(),
    "PresentationLayer\\Routes\\EInvalidPermissions");

/** Catch all exceptions raised by invalid parameters */
$router->registerExceptionHandler(new \PresentationLayer\Routing\ExceptionHandler\ParametersMissing(),
    "Json\\ValidationException");

$router->registerExceptionHandler(new \PresentationLayer\Routing\ExceptionHandler\ParametersMissing(),
    "PresentationLayer\\Routes\\EInvalidInputs");

$configuration = $container->get("ConfigurationLayer\\ApplicationConfig\\ApplicationConfig");

$router->slim()->config("debug", $configuration->get("slim_debug"));

/** Install custom Slim middleware */
$gZip = new \AaronSaray\SlimPHPMiddleware\Compress();
$router->slim()->add($gZip);

$router->initialize(ROOT_PATH . "/source/routes.xml");