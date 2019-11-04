<?php

namespace PresentationLayer\Routes\Admin\TimeSeries\Duplicates\Get;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\Admin\AdminRoute;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Admin\TimeSeries\Duplicates\Get
 */
class Get extends UserInferredRoute
{

    /** __construct
     *
     *  Constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager) {
		parent::__construct($sessionService, $entityManager);
    }

    public function execute() {
		parent::execute();

		$timeSeriesRepository = $this->entityManager->getRepository(PersistedTimeSeries::class);

		$query = '
		    SELECT ts.id, duplicates, ts.name, ts.hash
		    FROM timeseries ts
		    INNER JOIN (
                SELECT SUBSTRING(MIN(CONCAT(ts2.timestamp_created, "_", ts2.id)), 21, 36) id, COUNT(ts2.hash) duplicates FROM timeseries ts2 GROUP BY ts2.hash HAVING duplicates > 1
		    ) dups ON dups.id = ts.id
		';

        $duplicateTimeSeries = $this->entityManager->getConnection()->query($query)->fetchAll();

        $this->response->setReturnBody(new JSONBody(compact("duplicateTimeSeries")));
    }
}
