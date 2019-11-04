<?php

namespace PresentationLayer\Routes\Timeseries\Metadata\Categories\Get;

use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\Category\Repository\ICategoryRepository;
use DomainLayer\TimeSeriesManagement\Metadata\Categories\CategoryRenderer\CategoryRenderer;
use PresentationLayer\Routes\EInvalidInputs;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Timeseries\Metadata\Categories\Get
 */
class Get extends AbstractRoute{

    /** $categoryRepository
     *
     *  Repository interface for accessing categories.
     *
     * @var ICategoryRepository
     */
    private $categoryRepository;

    /** $categoryRenderer
     *
     *  Service used to convert categories into JSON-capable
     *  arrays.
     *
     * @var CategoryRenderer
     */
    private $categoryRenderer;

    /** __construct
     *
     *  Get constructor.
     *
     * @param ICategoryRepository $categoryRepository
     * @param CategoryRenderer $categoryRenderer
     */
    public function __construct(ICategoryRepository $categoryRepository, CategoryRenderer $categoryRenderer){
        $this->categoryRepository = $categoryRepository;
        $this->categoryRenderer = $categoryRenderer;
    }

    /** execute
     *
     *  Route execution.
     *
     */
    public function execute(){
        if (isset($_GET["node"])){
            $parentCategory = $this->categoryRepository->findById($_GET["node"]);

            /** Bail out if we have an invalid ID */
            if (NULL === $parentCategory) {
                throw new EInvalidInputs("Invalid parent category ID " . $_GET["node"]);
            }

            $categories = $parentCategory->getChildren();
        }else{
            $categories = $this->categoryRepository->getRootCategories();
        }

        $renderedResults = array();
        foreach($categories as $aCategory){
            if ("Unassigned" === $aCategory->getName()) {
                continue;
            }
            /** @var $aCategory Category */
            $renderedResults[] = $this->categoryRenderer->renderCategory($aCategory);
        }

        $this->response->setReturnBody(new JSONBody($renderedResults));
    }

}