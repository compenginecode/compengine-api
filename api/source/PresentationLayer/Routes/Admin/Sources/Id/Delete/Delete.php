<?php

namespace PresentationLayer\Routes\Admin\Sources\Id\Delete;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Source\Source;
use DomainLayer\ORM\Source\Repository\ISourceRepository;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\Admin\AdminRoute;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Delete
 * @package PresentationLayer\Routes\Admin\Sources\Id\Delete
 */
class Delete extends UserInferredRoute
{

    /** sourceRepository
     *
     *
     *
     * @var ISourceRepository
     */
    private $sourceRepository;

    /** __construct
     *
     *  Constructor
     *
     * @param EntityManager $entityManager
     * @param ISourceRepository $sourceRepository
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager,
		ISourceRepository $sourceRepository) {

		parent::__construct($sessionService, $entityManager);
        $this->sourceRepository = $sourceRepository;
    }

    public function execute() {
		parent::execute();

		/** @var Source $source */
        $source = $this->sourceRepository->find($this->queryParams[0]);

        /** Check source exists */
        if (null === $source) {
            Throw new EInvalidInputs("Source not found");
        }

        /** Persist removal */
        try {
            $this->entityManager->remove($source);
            $this->entityManager->flush();
        } catch (ForeignKeyConstraintViolationException $e) {
            Throw new EInvalidInputs("Source cannot be deleted as it is attached to existing time series.");
        }

        /** Return new source, rendered */
        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
