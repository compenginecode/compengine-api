<?php

namespace PresentationLayer\Routes\Admin\Categories\Id\Delete;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\Category\Repository\ICategoryRepository;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\Admin\AdminRoute;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Delete
 * @package PresentationLayer\Routes\Admin\Categories\Id\Delete
 */
class Delete extends UserInferredRoute
{

    /** sourceRepository
     *
     *
     *
     * @var ICategoryRepository
     */
    private $sourceRepository;

    /** __construct
     *
     *  Constructor
     *
     * @param EntityManager $entityManager
     * @param ICategoryRepository $sourceRepository
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager,
		ICategoryRepository $sourceRepository) {

		parent::__construct($sessionService, $entityManager);

        $this->entityManager = $entityManager;
        $this->sourceRepository = $sourceRepository;
    }

    public function execute() {
		parent::execute();

		/** @var Category $source */
        $source = $this->sourceRepository->findById($this->queryParams[0]);

        /** Check source exists */
        if (null === $source) {
            Throw new EInvalidInputs("Category not found");
        }

        /** Persist removal */
        try {
            $this->entityManager->remove($source);
            $this->entityManager->flush();
        } catch (ForeignKeyConstraintViolationException $e) {
            Throw new EInvalidInputs("Category cannot be deleted as it is attached to existing time series or has child categories.");
        }

        /** Return new source, rendered */
        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
