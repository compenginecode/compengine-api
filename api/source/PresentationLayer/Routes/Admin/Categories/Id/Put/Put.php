<?php

namespace PresentationLayer\Routes\Admin\Categories\Id\Put;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\Category\Repository\ICategoryRepository;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\Admin\AdminRoute;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Put
 * @package PresentationLayer\Routes\Admin\Categories\Id\Put
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

		$webRequest = $this->request->getBodyAsArray();

        /** @var Category $category */
        $category = $this->categoryRepository->findById($this->queryParams[0]);

        /** Check category exists */
        if (null === $category) {
            Throw new EInvalidInputs("Category not found");
        }

        /** Check that category name is provided */
        if (!isset($webRequest["name"]) || empty($webRequest["name"])) {
            Throw new EInvalidInputs("name field is required");
        }

        /** Check category doesnt already exist.
         *  Ignore if same name is same as before. */
        if ($this->categoryRepository->findByName($webRequest["name"]) && strtolower($webRequest["name"]) !== strtolower($category->getName())) {
            Throw new EInvalidInputs("Category already exists");
        }

        /** Set parent if provided */
        if (array_key_exists("parentId", $webRequest)) {
            if (!empty($webRequest["parentId"])) {
                $parent = $this->categoryRepository->findById($webRequest["parentId"]);

                if (null === $parent) {
                    Throw new EInvalidInputs("parent not found");
                }
            } else {
                $parent = null;
            }

            $category->setParentCategory($parent);
        }

        /** Persist update */
        $category->setName($webRequest["name"]);
        $category->setApprovalStatus(ApprovalStatus::approved());
        $this->entityManager->flush();
        $this->entityManager->refresh($category);

        /** Return new category, rendered */
        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
