<?php

namespace PresentationLayer\Routes\Timeseries\Export\SearchResults\Post;

use ConfigurationLayer\ApplicationConfig\ApplicationConfig;
use DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService\TimeSeriesDownloadService;
use InfrastructureLayer\Caching\CacheAdaptor\ICacheAdaptor;
use InfrastructureLayer\Crypto\TokenGenerator\ITokenGenerator;
use PresentationLayer\Routes\EBadRequest;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\Timeseries\SearchQueryFactory;
use PresentationLayer\Routing\StatusCode\UnprocessableEntity;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;
use Yam\Route\Response\StatusCode\StatusBadRequest;

/**
 * Class Post
 * @package PresentationLayer\Routes\Timeseries\Export\SearchResults\Post
 */
class Post extends AbstractRoute {

    private $cacheAdaptor;

    private $tokenGenerator;

    public function __construct(ICacheAdaptor $cacheAdaptor, ITokenGenerator $tokenGenerator){
		$this->cacheAdaptor = $cacheAdaptor;
		$this->tokenGenerator = $tokenGenerator;
    }

    public function execute() {
    	if (!isset($_POST["ids"])){
    		throw new EInvalidInputs("The ids parameter must be set.");
		}

		$token = $this->tokenGenerator->generateToken(32);
    	$data = array(
    		"ids" => $_POST["ids"],
			"type" => $_POST["type"]
		);

		$this->cacheAdaptor->setValue($token, serialize($data), 24*60*60);
		$this->response->setReturnBody(new JSONBody(array(
			"message" => "success",
			"token" => $token
		)));
    }

}