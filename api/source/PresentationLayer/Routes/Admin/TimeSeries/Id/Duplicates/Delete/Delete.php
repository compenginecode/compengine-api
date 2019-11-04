<?php

namespace PresentationLayer\Routes\Admin\TimeSeries\Id\Duplicates\Delete;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Delete
 * @package PresentationLayer\Routes\Admin\TimeSeries\Id\Duplicates\Delete
 */
class Delete extends UserInferredRoute
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

		/** @var PersistedTimeSeries $timeSeries */
        $timeSeries = $this->entityManager->find(PersistedTimeSeries::class, $this->queryParams[0]);

        if (is_null($timeSeries)) {
            Throw new EInvalidInputs("Time series does not exist");
        }

        $timeSeriesRepository = $this->entityManager->getRepository(PersistedTimeSeries::class);
        $duplicateTimeSeries = $timeSeriesRepository->createQueryBuilder("ts")
            ->where("ts.hash = :hash")
            ->andWhere("ts.id != :id")
            ->getQuery()->execute(["id" => $timeSeries->getId(), "hash" => $timeSeries->getHash()]);

        array_walk($duplicateTimeSeries, function (PersistedTimeSeries $duplicateTimeSeries) {
            $this->entityManager->remove($duplicateTimeSeries);
        });

        $this->entityManager->flush();

        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
