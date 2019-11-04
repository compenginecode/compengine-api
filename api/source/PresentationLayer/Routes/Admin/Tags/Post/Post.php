<?php

namespace PresentationLayer\Routes\Admin\Tags\Post;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\Tag\Repository\ITagRepository;
use DomainLayer\ORM\Tag\Tag;
use DomainLayer\TimeSeriesManagement\Metadata\Tags\TagRenderer\TagRenderer;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\Admin\AdminRoute;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\Admin\Tags\Post
 */
class Post extends UserInferredRoute
{

    /** tagRenderer
     *
     *
     *
     * @var TagRenderer
     */
    private $tagRenderer;

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
     * @param TagRenderer $tagRenderer
     * @param ITagRepository $tagRepository
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager, TagRenderer $tagRenderer,
		ITagRepository $tagRepository) {

		parent::__construct($sessionService, $entityManager);

        $this->tagRenderer = $tagRenderer;
        $this->tagRepository = $tagRepository;
    }

    public function execute() {
		parent::execute();

		$webRequest = $this->request->getBodyAsArray();

        /** Check that tag name is provided */
        if (!isset($webRequest["name"]) || empty($webRequest["name"])) {
            Throw new EInvalidInputs("name field is required");
        }

        /** Check tag doesnt already exist */
        if ($this->tagRepository->findByName($webRequest["name"])) {
            Throw new EInvalidInputs("Tag already exists");
        }

        /** Persist tag */
        $tag = new Tag($webRequest["name"], ApprovalStatus::approved());
        $this->entityManager->persist($tag);
        $this->entityManager->flush();
        $this->entityManager->refresh($tag);

        /** Return new tag, rendered */
        $this->response->setReturnBody(new JSONBody($this->tagRenderer->renderTag($tag)));
    }
}
