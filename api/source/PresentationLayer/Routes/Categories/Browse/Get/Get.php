<?php

namespace PresentationLayer\Routes\Categories\Browse\Get;

use DomainLayer\ORM\Category\Repository\ICategoryRepository;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Categories\Browse\Get
 */
class Get extends AbstractRoute
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
     * @param ICategoryRepository $categoryRepository
     */
    public function __construct(ICategoryRepository $categoryRepository) {
        $this->categoryRepository = $categoryRepository;
    }

    public function execute() {
        $time1 = microtime(true);
        $categories = $this->categoryRepository->listAll();
        $this->applyTotalsRecursively($categories); // don't use recursive counts
        $time2 = microtime(true);

        $this->response->setReturnBody(new JSONBody([
            "categories" => $categories,
            "total" => $this->recursiveCount($categories),
            "time" => round($time2 - $time1, 3),
        ]));
    }

    /** recursiveCount
     *
     *  Return the sum of all categories and their children recursively.
     *
     * @param $categories
     * @return int
     */
    private function recursiveCount($categories) {
        return array_reduce($categories, function ($carry, $category) {
            return $carry + $this->recursiveCount($category["children"]);
        },  count($categories));
    }

    /** applyTotalsRecursively
     *
     *  Apply the recursive total to each category.
     *
     * @param $categories
     */
    private function applyTotalsRecursively(&$categories) {
        array_walk($categories, function (&$category) {
            /**
             * Dig down into the structure first
             */
            $this->applyTotalsRecursively($category["children"]);
            /**
             * Apply the updated total as you climb back out of the structure
             */
            $category["total"] = array_reduce($category["children"], function ($carry, $category) {
                return $carry + $category["total"];
            }, $category["total"]);
        });
    }
}
