<?php

namespace PresentationLayer\Routes\Admin\Sources\Id\Put;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Source\Source;
use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\Source\Repository\ISourceRepository;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\Admin\AdminRoute;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Put
 * @package PresentationLayer\Routes\Admin\Sources\Id\Put
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

		$webRequest = $this->request->getBodyAsArray();

        /** @var Source $source */
        $source = $this->sourceRepository->find($this->queryParams[0]);

        /** Check source exists */
        if (null === $source) {
            Throw new EInvalidInputs("Source not found");
        }

        /** Check that source name is provided */
        if (!isset($webRequest["name"]) || empty($webRequest["name"])) {
            Throw new EInvalidInputs("name field is required");
        }

        /** Check source doesnt already exist.
         *  Ignore if same name is same as before. */
        if ($this->sourceRepository->findOneByName($webRequest["name"]) && strtolower($webRequest["name"]) !== strtolower($source->getName())) {
            Throw new EInvalidInputs("Source already exists");
        }

        /** Persist update */
        $source->setName($webRequest["name"]);
        $source->setApprovalStatus(ApprovalStatus::approved());
        $this->entityManager->flush();
        $this->entityManager->refresh($source);

        /** Return new source, rendered */
        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
