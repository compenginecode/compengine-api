<?php

namespace PresentationLayer\Routes\Admin\Contributors\Get;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use DomainLayer\ORM\Contributor\Contributor;
use DomainLayer\ORM\Contributor\Repository\IContributorRepository;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Admin\Contributors\Get
 */
class Get extends UserInferredRoute
{
    /** contributorsRepositiory
     *
     *
     *
     * @var IContributorRepository|EntityRepository
     */
    private $contributorRepository;

    /** __construct
     *
     *  Constructor
     *
     * @param SessionService $sessionService
     * @param EntityManager $entityManager
     * @param IContributorRepository $contributorRepository
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager,
                                IContributorRepository $contributorRepository) {

        parent::__construct($sessionService, $entityManager);
        $this->contributorRepository = $contributorRepository;
    }

    public function execute() {
        parent::execute();

        $contributors = $this->contributorRepository->findAll();

        $contributors = array_map(function (Contributor $contributor) {
            return [
                "id" => $contributor->getId(),
                "name" => $contributor->getName(),
                "email" => $contributor->getEmailAddress()
            ];
        }, $contributors);

        $this->response->setReturnBody(new JSONBody(compact("contributors")));
    }
}
