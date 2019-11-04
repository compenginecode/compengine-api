<?php

namespace PresentationLayer\Routes\Admin\Tags\Id\Approve\Put;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\Tag\Tag;
use DomainLayer\ORM\Tag\Repository\ITagRepository;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
     * Class Put
     * @package PresentationLayer\Routes\Admin\Tags\Id\Approve\Put
     */
class Put extends UserInferredRoute
{
    /** tagRepository
     *
     *
     *
     * @var ITagRepository
     */
    private $tagRepository;

    /** __construct
     *
     *  Constructor
     *
     * @param SessionService $sessionService
     * @param EntityManager $entityManager
     * @param ITagRepository $tagRepository
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager,
                                ITagRepository $tagRepository) {

        parent::__construct($sessionService, $entityManager);
        $this->tagRepository = $tagRepository;
    }

    public function execute() {
        parent::execute();

        /** @var Tag $tag */
        $tag = $this->tagRepository->find($this->queryParams[0]);

        /** Check tag exists */
        if (null === $tag) {
            Throw new EInvalidInputs("Tag not found");
        }

        $tag->setApprovalStatus(ApprovalStatus::approved());

        $this->entityManager->flush();

        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
