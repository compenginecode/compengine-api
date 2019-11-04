<?php

namespace PresentationLayer\Routes\Admin\Tags\Get;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Tag\Repository\ITagRepository;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\Admin\AdminRoute;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Admin\Tags\Get
 */
class Get extends UserInferredRoute
{
    /** tagsRepositiory
     *
     *
     *
     * @var ITagRepository
     */
    private $tagRepositiory;

    /** __construct
     *
     *  Constructor
     *
     * @param ITagRepository $tagRepositiory
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager,
		ITagRepository $tagRepositiory) {

		parent::__construct($sessionService, $entityManager);
        $this->tagRepositiory = $tagRepositiory;
    }

    public function execute() {
		parent::execute();

		$page = isset($_GET["page"]) && !empty($_GET["page"]) ? $_GET["page"] : 1;
        $paginatedTags = $this->tagRepositiory->paginate($page);

        $this->response->setReturnBody(new JSONBody($paginatedTags));
    }
}
