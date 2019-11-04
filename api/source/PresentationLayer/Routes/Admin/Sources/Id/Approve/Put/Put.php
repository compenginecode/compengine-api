<?php

namespace PresentationLayer\Routes\Admin\Sources\Id\Approve\Put;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\Source\Source;
use DomainLayer\ORM\Source\Repository\ISourceRepository;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
     * Class Put
     * @package PresentationLayer\Routes\Admin\Sources\Id\Approve\Put
     */
class Put extends UserInferredRoute
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
     * @param SessionService $sessionService
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

        $source->setApprovalStatus(ApprovalStatus::approved());

        $this->entityManager->flush();

        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
