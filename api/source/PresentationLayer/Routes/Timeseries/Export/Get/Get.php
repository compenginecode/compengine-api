<?php

namespace PresentationLayer\Routes\Timeseries\Export\Get;

use ConfigurationLayer\ApplicationConfig\ApplicationConfig;
use DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService\TimeSeriesDownloadService;
use PresentationLayer\Routes\EBadRequest;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routing\StatusCode\UnprocessableEntity;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;
use Yam\Route\Response\StatusCode\StatusBadRequest;

/**
 * Class Get
 * @package PresentationLayer\Routes\Timeseries\Export\Get
 */
class Get extends AbstractRoute {

    private $service;
    private $config;

    public function __construct(TimeSeriesDownloadService $service, ApplicationConfig $config) {
        $this->service = $service;
        $this->config = $config;
    }

    public function execute() {
        try {
            if (empty($_GET["token"])) {
                throw new \Exception("No download token present.");
            }

            $this->service->downloadTimeSeries($_GET["token"]);
            die();
        } catch (\Exception $e) {
            header("Location: " . $this->config->get("frontend_url") . "/#!oh-no?message=" . $e->getMessage());
            die();
        } finally {
            $this->response->setReturnBody(new JSONBody($response));
        }
    }

}