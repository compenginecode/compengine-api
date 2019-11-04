<?php

namespace PresentationLayer\Routes\Admin\Sources\Id\Deny\Post;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Source\Source;
use DomainLayer\ORM\Source\Repository\ISourceRepository;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository\DatabaseTimeSeriesRepository;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\Admin\Sources\Id\Deny\Post
 */
class Post extends UserInferredRoute
{
    /** sourceRepository
     *
     *
     *
     * @var ISourceRepository
     */
    private $sourceRepository;

    /** timeSeriesRepository
     *
     *
     *
     * @var DatabaseTimeSeriesRepository
     */
    private $timeSeriesRepository;

    /** __construct
     *
     *  Constructor
     *
     * @param SessionService $sessionService
     * @param EntityManager $entityManager
     * @param ISourceRepository $sourceRepository
     * @param DatabaseTimeSeriesRepository $timeSeriesRepository
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager,
                                ISourceRepository $sourceRepository, DatabaseTimeSeriesRepository $timeSeriesRepository) {

        parent::__construct($sessionService, $entityManager);
        $this->sourceRepository = $sourceRepository;
        $this->timeSeriesRepository = $timeSeriesRepository;
    }

    public function execute() {
        parent::execute();

        /** @var Source $source */
        $source = $this->sourceRepository->find($this->queryParams[0]);

        /** Check source exists */
        if (null === $source) {
            Throw new EInvalidInputs("Source not found");
        }

        $webRequest = $this->request->getBodyAsArray();

        /** Check client provided required replacementSourceId parameter */
        if (empty($webRequest["replacementSourceId"])) {
            Throw new EInvalidInputs("Replacement source is required");
        }

        /** @var Source $replacementSource */
        $replacementSource = $this->sourceRepository->find($webRequest["replacementSourceId"]);

        /** Check replacement source exists */
        if (null === $replacementSource) {
            Throw new EInvalidInputs("Replacement source not found");
        }

        /** Check replacement source is different to denied source */
        if ($replacementSource === $source) {
            Throw new EInvalidInputs("Replacement source must be different to denied source");
        }

        /** Get time series that belong to the denied source */
        $timeSeries = $this->timeSeriesRepository->findBy(["source" => $source]);

        /** Iterate through each time series and set the source to the replacement source */
        array_walk($timeSeries, function (PersistedTimeSeries $timeSeries) use ($replacementSource) {
            $timeSeries->setSource($replacementSource);
        });

        /** Delete the denied source and persist time series updates */
        $this->entityManager->remove($source);
        $this->entityManager->flush();

        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
