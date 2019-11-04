<?php

namespace PresentationLayer\Routes\Admin\Contributors\Id\Delete;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Contributor\Contributor;
use DomainLayer\ORM\Contributor\Repository\IContributorRepository;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository\ITimeSeriesRepository;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Delete
 * @package PresentationLayer\Routes\Admin\Contributors\Id\Delete
 */
class Delete extends UserInferredRoute
{

    /** contributorRepository
     *
     *
     *
     * @var IContributorRepository
     */
    private $contributorRepository;

    /** timeSeriesRepository
     *
     *
     *
     * @var ITimeSeriesRepository
     */
    private $timeSeriesRepository;

    /** __construct
     *
     *  Constructor
     *
     * @param EntityManager $entityManager
     * @param IContributorRepository $contributorRepository
     * @param ITimeSeriesRepository $timeSeriesRepository
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager,
                                IContributorRepository $contributorRepository, ITimeSeriesRepository $timeSeriesRepository) {
        parent::__construct($sessionService, $entityManager);
        $this->contributorRepository = $contributorRepository;
        $this->timeSeriesRepository = $timeSeriesRepository;
    }

    public function execute() {
        parent::execute();

        /** @var Contributor $contributor */
        $contributor = $this->entityManager->find(Contributor::class, $this->queryParams[0]);

        /** Check contributor exists */
        if (null === $contributor) {
            Throw new EInvalidInputs("Contributor not found");
        }

        /** Unset the contributor on any time series belonging to it */
        $timeSeries = $this->timeSeriesRepository->createQueryBuilder("ts")
            ->where("ts.contributor = :contributor")->setParameter("contributor", $contributor)->getQuery()->execute();

        array_walk($timeSeries, function (PersistedTimeSeries $timeSeries) {
            $timeSeries->setContributor(null);
        });

        $this->entityManager->remove($contributor);
        $this->entityManager->flush();

        /** Return new contributor, rendered */
        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
