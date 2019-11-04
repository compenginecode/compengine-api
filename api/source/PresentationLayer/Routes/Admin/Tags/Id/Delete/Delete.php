<?php

namespace PresentationLayer\Routes\Admin\Tags\Id\Delete;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Tag\Repository\ITagRepository;
use DomainLayer\ORM\Tag\Tag;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\Admin\AdminRoute;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Delete
 * @package PresentationLayer\Routes\Admin\Tags\Id\Delete
 */
class Delete extends UserInferredRoute
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
     * @param EntityManager $entityManager
     * @param ITagRepository $tagRepository
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager, ITagRepository $tagRepository) {
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

        /** Persist removal */
        $this->entityManager->remove($tag);
        $this->entityManager->flush();

        /** Return new tag, rendered */
        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
