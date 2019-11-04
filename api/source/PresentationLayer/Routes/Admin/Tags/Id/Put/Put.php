<?php

namespace PresentationLayer\Routes\Admin\Tags\Id\Put;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\Tag\Repository\ITagRepository;
use DomainLayer\ORM\Tag\Tag;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\Admin\AdminRoute;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Put
 * @package PresentationLayer\Routes\Admin\Tags\Id\Put
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
     * @param EntityManager $entityManager
     * @param ITagRepository $tagRepository
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager, ITagRepository $tagRepository) {
		parent::__construct($sessionService, $entityManager);
        $this->tagRepository = $tagRepository;
    }

    public function execute() {
		parent::execute();

		$webRequest = $this->request->getBodyAsArray();

        /** @var Tag $tag */
        $tag = $this->tagRepository->find($this->queryParams[0]);

        /** Check tag exists */
        if (null === $tag) {
            Throw new EInvalidInputs("Tag not found");
        }

        /** Check that tag name is provided */
        if (!isset($webRequest["name"]) || empty($webRequest["name"])) {
            Throw new EInvalidInputs("name field is required");
        }

        /** Check tag doesnt already exist.
         *  Ignore if same name is same as before. */
        if ($this->tagRepository->findByName($webRequest["name"]) && strtolower($webRequest["name"]) !== strtolower($tag->getName())) {
            Throw new EInvalidInputs("Tag already exists");
        }

        /** Persist update */
        $tag->setName($webRequest["name"]);
        $tag->setApprovalStatus(ApprovalStatus::approved());
        $this->entityManager->flush();
        $this->entityManager->refresh($tag);

        /** Return new tag, rendered */
        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
