<?php

namespace PresentationLayer\Routes\Admin\Categories\Id\Approve\Put;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\Category\Repository\ICategoryRepository;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
     * Class Put
     * @package PresentationLayer\Routes\Admin\Categories\Id\Approve\Put
     */
class Put extends UserInferredRoute
{
    /** categoryRepository
     *
     *
     *
     * @var ICategoryRepository
     */
    private $categoryRepository;

    /** __construct
     *
     *  Constructor
     *
     * @param SessionService $sessionService
     * @param EntityManager $entityManager
     * @param ICategoryRepository $categoryRepository
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager,
                                ICategoryRepository $categoryRepository) {

        parent::__construct($sessionService, $entityManager);
        $this->categoryRepository = $categoryRepository;
    }

    public function execute() {
        parent::execute();

        /** @var Category $category */
        $category = $this->categoryRepository->findById($this->queryParams[0]);

        /** Check category exists */
        if (null === $category) {
            Throw new EInvalidInputs("Category not found");
        }

        $category->setApprovalStatus(ApprovalStatus::approved());

        $this->entityManager->flush();

        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
