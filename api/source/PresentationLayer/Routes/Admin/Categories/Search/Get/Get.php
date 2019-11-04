<?php

namespace PresentationLayer\Routes\Admin\Categories\Search\Get;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Category\Repository\ICategoryRepository;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Admin\Categories\Search\Get
 */
class Get extends UserInferredRoute
{
    /** categoriesRepositiory
     *
     *
     *
     * @var ICategoryRepository
     */
    private $categoryRepositiory;

    /** __construct
     *
     *  Constructor
     *
     * @param ICategoryRepository $categoryRepositiory
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager,
                                ICategoryRepository $categoryRepositiory) {

        parent::__construct($sessionService, $entityManager);
        $this->categoryRepositiory = $categoryRepositiory;
    }

    public function execute() {
        parent::execute();

        $name = isset($_GET["keyword"]) ? $_GET["keyword"] : '';

        $nestedCategories = $this->categoryRepositiory->adminSearchAll($name);

        $this->response->setReturnBody(new JSONBody($nestedCategories));
    }
}
