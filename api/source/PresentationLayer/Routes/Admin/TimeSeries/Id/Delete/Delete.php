<?php

namespace PresentationLayer\Routes\Admin\TimeSeries\Id\Delete;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use InfrastructureLayer\ElasticSearch\ElasticSearch;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Delete
 * @package PresentationLayer\Routes\Admin\TimeSeries\Id\Delete
 */
class Delete extends UserInferredRoute
{

    /** elasticSearch
     *
     *
     *
     * @var ElasticSearch
     */
    private $elasticSearch;

    /** siteAttributeRepository
     *
     *
     *
     * @var ISiteAttributeRepository
     */
    private $siteAttributeRepository;

    /** __construct
     *
     *  Constructor
     *
     * @param EntityManager $entityManager
     * @param ElasticSearch $elasticSearch
     * @param ISiteAttributeRepository $siteAttributeRepository
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager, ElasticSearch $elasticSearch,
		ISiteAttributeRepository $siteAttributeRepository) {

		parent::__construct($sessionService, $entityManager);
        $this->elasticSearch = $elasticSearch;
        $this->siteAttributeRepository = $siteAttributeRepository;
    }

    public function execute() {
		parent::execute();

		/** @var PersistedTimeSeries $timeSeries */
        $timeSeries = $this->entityManager->find(PersistedTimeSeries::class, $this->queryParams[0]);

        if (is_null($timeSeries)) {
            Throw new EInvalidInputs("Time series does not exist");
        }

        $this->elasticSearch->removeFeatureVectorDocument(
            $timeSeries->getDocumentId(),
            $this->siteAttributeRepository->getCurrentFeatureVectorFamily()->getIndexName()
        );

        $this->entityManager->remove($timeSeries);
        $this->entityManager->flush();

        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
