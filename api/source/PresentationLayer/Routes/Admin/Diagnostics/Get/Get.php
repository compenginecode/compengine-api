<?php

namespace PresentationLayer\Routes\Admin\Diagnostics\Get;

use DomainLayer\TimeSeriesManagement\Diagnostics\CentralMeasureDiagnosticService;
use InfrastructureLayer\Process\HealthCheck\HealthCheckService\HealthCheckService;
use PresentationLayer\ReturnBody\TextReturnBody;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Admin\Diagnostics\Get
 */
class Get extends AbstractRoute{

    private $healthCheckService;

    private $centralMeasureDiagnosticService;

    public function __construct(HealthCheckService $healthCheckService,
        CentralMeasureDiagnosticService $centralMeasureDiagnosticService){

        $this->healthCheckService = $healthCheckService;
        $this->centralMeasureDiagnosticService = $centralMeasureDiagnosticService;
    }

    /** execute
     *
     *  Route execution.
     *
     */
    public function execute(){
        $services = array(
            "queue-delegator-a"
        );

        $mockResponseObj = array("processes" => []);
        foreach($services as $aService){
            $mockResponseObj["processes"][] = array(
                "name" => $aService,
                "status" => $this->healthCheckService->getHealthStatus($aService)->chosenOption()
            );
        }

        $mockResponseObj["centralMeasures"] = $this->centralMeasureDiagnosticService->getDiagnosticInformation();

        $this->response->setReturnBody(new JSONBody($mockResponseObj));
    }

}