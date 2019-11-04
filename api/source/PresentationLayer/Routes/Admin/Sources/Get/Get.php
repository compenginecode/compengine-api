<?php

namespace PresentationLayer\Routes\Admin\Sources\Get;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Source\Repository\ISourceRepository;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Admin\Sources\Get
 */
class Get extends UserInferredRoute
{
    /** sourcesRepositiory
     *
     *
     *
     * @var ISourceRepository
     */
    private $sourceRepositiory;

    /** __construct
     *
     *  Constructor
     *
     * @param ISourceRepository $sourceRepositiory
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager,
		ISourceRepository $sourceRepositiory) {

		parent::__construct($sessionService, $entityManager);
        $this->sourceRepositiory = $sourceRepositiory;
    }

    public function execute() {
		parent::execute();

		$page = isset($_GET["page"]) && !empty($_GET["page"]) ? $_GET["page"] : 1;
        $paginatedSources = $this->sourceRepositiory->paginate($page);

        $this->response->setReturnBody(new JSONBody($paginatedSources));
    }
}
