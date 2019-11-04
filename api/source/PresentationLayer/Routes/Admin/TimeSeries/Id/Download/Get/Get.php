<?php

namespace PresentationLayer\Routes\Admin\TimeSeries\Id\Download\Get;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\TimeSeries\BulkUploadedTimeSeries\BulkUploadedTimeSeries;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\StringBody\StringBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Admin\TimeSeries\Id\Download\Get
 */
class Get extends AbstractRoute
{

	private $entityManager;

    /** __construct
     *
     *  Constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager) {
    	$this->entityManager = $entityManager;
    }

    public function execute() {
		/** @var BulkUploadedTimeSeries $bulkUploadedTimeSeries */
        $bulkUploadedTimeSeries = $this->entityManager->find(BulkUploadedTimeSeries::class, $this->queryParams[0]);

        if (is_null($bulkUploadedTimeSeries)) {
            Throw new EInvalidInputs("Time series does not exist");
        }

        $dataPoints = $bulkUploadedTimeSeries->getDataPoints();
        $csv = implode("\n", $dataPoints);

        $this->response->setHeader("Content-Type", "application/csv");
        $this->response->setHeader("Content-Disposition", "attachment;filename={$bulkUploadedTimeSeries->getName()}.csv");
        $this->response->setReturnBody(new StringBody($csv));
    }
}
