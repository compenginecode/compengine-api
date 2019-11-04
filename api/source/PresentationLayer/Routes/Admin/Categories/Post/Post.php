<?php

namespace PresentationLayer\Routes\Admin\Categories\Post;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\Category\Repository\ICategoryRepository;
use DomainLayer\TimeSeriesManagement\Metadata\Categories\CategoryRenderer\CategoryRenderer;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\Admin\AdminRoute;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\Admin\Categories\Post
 */
class Post extends UserInferredRoute
{

    /** categoryRenderer
     *
     *
     *
     * @var CategoryRenderer
     */
    private $categoryRenderer;

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
     * @param CategoryRenderer $categoryRenderer
     * @param ICategoryRepository $categoryRepository
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager,
		CategoryRenderer $categoryRenderer, ICategoryRepository $categoryRepository) {

		parent::__construct($sessionService, $entityManager);

        $this->categoryRenderer = $categoryRenderer;
        $this->categoryRepository = $categoryRepository;
    }

    public function execute() {
		parent::execute();

		$webRequest = $this->request->getBodyAsArray();

        /** Check that category name is provided */
        if (!isset($webRequest["name"]) || empty($webRequest["name"])) {
            Throw new EInvalidInputs("name field is required");
        }

        /** Check category doesnt already exist */
        if ($this->categoryRepository->findByName($webRequest["name"])) {
            Throw new EInvalidInputs("Category already exists");
        }

        if (!array_key_exists("parentId", $webRequest)) {
            Throw new EInvalidInputs("parentId must be provided (can be null)");
        } else if (!empty($webRequest["parentId"])) {
            $parent = $this->categoryRepository->findById($webRequest["parentId"]);

            if (null === $parent) {
                Throw new EInvalidInputs("parent not found");
            }
        } else {
            $parent = null;
        }

        /** Persist category */
        $category = new Category($webRequest["name"], $parent);
        $category->setApprovalStatus(ApprovalStatus::approved());
        $this->entityManager->persist($category);
        $this->entityManager->flush();
        $this->entityManager->refresh($category);

        /** Return new category, rendered */
        $this->response->setReturnBody(new JSONBody($this->categoryRenderer->renderCategory($category)));
    }
}
