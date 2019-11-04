<?php

namespace PresentationLayer\Routes\Admin\Sources\Post;
use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\Source\Repository\ISourceRepository;
use DomainLayer\ORM\Source\Source;
use DomainLayer\TimeSeriesManagement\Metadata\Sources\SourceRenderer\SourceRenderer;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\Admin\AdminRoute;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\Admin\Sources\Post
 */
class Post extends UserInferredRoute
{

    /** sourceRenderer
     *
     *
     *
     * @var SourceRenderer
     */
    private $sourceRenderer;

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
     * @param SourceRenderer $sourceRenderer
     * @param ISourceRepository $sourceRepository
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager,
		SourceRenderer $sourceRenderer, ISourceRepository $sourceRepository) {

		parent::__construct($sessionService, $entityManager);

        $this->sourceRenderer = $sourceRenderer;
        $this->sourceRepository = $sourceRepository;
    }

    public function execute() {
		parent::execute();

		$webRequest = $this->request->getBodyAsArray();

        /** Check that source name is provided */
        if (!isset($webRequest["name"]) || empty($webRequest["name"])) {
            Throw new EInvalidInputs("name field is required");
        }

        /** Check source doesnt already exist */
        if ($this->sourceRepository->findOneByName($webRequest["name"])) {
            Throw new EInvalidInputs("Source already exists");
        }

        /** Persist source */
        $source = new Source($webRequest["name"], ApprovalStatus::approved());
        $this->entityManager->persist($source);
        $this->entityManager->flush();
        $this->entityManager->refresh($source);

        /** Return new source, rendered */
        $this->response->setReturnBody(new JSONBody($this->sourceRenderer->renderSource($source)));
    }
}
