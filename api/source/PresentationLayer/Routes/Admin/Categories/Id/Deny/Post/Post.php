<?php

namespace PresentationLayer\Routes\Admin\Categories\Id\Deny\Post;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\Category\Repository\ICategoryRepository;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository\DatabaseTimeSeriesRepository;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\Admin\Categories\Id\Deny\Post
 */
class Post extends UserInferredRoute
{
    /** categoryRepository
     *
     *
     *
     * @var ICategoryRepository
     */
    private $categoryRepository;

    /** timeSeriesRepository
     *
     *
     *
     * @var DatabaseTimeSeriesRepository
     */
    private $timeSeriesRepository;

    /** __construct
     *
     *  Constructor
     *
     * @param SessionService $sessionService
     * @param EntityManager $entityManager
     * @param ICategoryRepository $categoryRepository
     * @param DatabaseTimeSeriesRepository $timeSeriesRepository
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager,
                                ICategoryRepository $categoryRepository, DatabaseTimeSeriesRepository $timeSeriesRepository) {

        parent::__construct($sessionService, $entityManager);
        $this->categoryRepository = $categoryRepository;
        $this->timeSeriesRepository = $timeSeriesRepository;
    }

    public function execute() {
        parent::execute();

        /** @var Category $category */
        $category = $this->categoryRepository->findById($this->queryParams[0]);

        /** Check category exists */
        if (null === $category) {
            Throw new EInvalidInputs("Category not found");
        }

        $webRequest = $this->request->getBodyAsArray();

        /** Check client provided required replacementCategoryId parameter */
        if (empty($webRequest["replacementCategoryId"])) {
            Throw new EInvalidInputs("Replacement category is required");
        }

        /** @var Category $replacementCategory */
        $replacementCategory = $this->categoryRepository->findById($webRequest["replacementCategoryId"]);

        /** Check replacement category exists */
        if (null === $replacementCategory) {
            Throw new EInvalidInputs("Replacement category not found");
        }

        /** Check replacement category is different to denied category */
        if ($replacementCategory === $category) {
            Throw new EInvalidInputs("Replacement category must be different to denied category");
        }

        /** Get time series that belong to the denied category */
        $timeSeries = $this->timeSeriesRepository->findBy(["category" => $category]);

        /** Iterate through each time series and set the category to the replacement category */
        array_walk($timeSeries, function (PersistedTimeSeries $timeSeries) use ($replacementCategory) {
            $timeSeries->setCategory($replacementCategory);
        });

        /** Delete the denied category and persist time series updates */
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
