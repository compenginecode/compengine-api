<?php

namespace PresentationLayer\Routes\Timeseries\Search;

use DomainLayer\ORM\Category\Repository\ICategoryRepository;
use DomainLayer\ORM\Source\Repository\ISourceRepository;
use DomainLayer\ORM\Tag\Repository\ITagRepository;
use DomainLayer\SearchService\ISearchRequest;
use PresentationLayer\Routes\EInvalidInputs;

/**
 * Class SearchWebRequest
 * @package PresentationLayer\Routes\Timeseries\Search
 */
class SearchWebRequest implements ISearchRequest
{
    const SEARCH_TYPES = ["term", "category", "tag", "source"];

    private $webRequest;

    /** categoryRepository
     *
     *
     *
     * @var ICategoryRepository
     */
    private $categoryRepository;

    /** tagRepository
     *
     *
     *
     * @var ITagRepository
     */
    private $tagRepository;

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
     * @param ICategoryRepository $categoryRepository
     * @param ITagRepository $tagRepository
     * @param ISourceRepository $sourceRepository
     */
    public function __construct(ICategoryRepository $categoryRepository, ITagRepository $tagRepository, ISourceRepository $sourceRepository) {
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
        $this->sourceRepository = $sourceRepository;
    }

    public function populate($webRequest) {
        if (count(array_intersect(self::SEARCH_TYPES, array_keys((array)$webRequest))) > 1) {
            throw new EInvalidInputs("Only ONE (term, source, category, tag) filter may be used at a time");
        }
        $this->checkPage($webRequest);
        $this->webRequest = $webRequest;
    }

    public function hasTerm() {
        return isset($this->webRequest["term"]);
    }

    public function getTerm() {
        return $this->webRequest["term"];
    }

    public function hasCategory() {
        return isset($this->webRequest["category"]);
    }

    public function getCategory() {
        return $this->categoryRepository->findBySlug($this->webRequest["category"]);
    }

    public function hasTag() {
        return isset($this->webRequest["tag"]);
    }

    public function getTag() {
        return $this->tagRepository->findBySlug($this->webRequest["tag"]);
    }

    public function hasSource() {
        return isset($this->webRequest["source"]);
    }

    public function getSource() {
        return $this->sourceRepository->findBySlug($this->webRequest["source"]);
    }

    public function checkPage($webRequest) {
        if (isset($webRequest["page"]) && ($webRequest["page"] < 1 || filter_var($webRequest["page"], FILTER_VALIDATE_INT) === false)) {
            throw new EInvalidInputs("Page number is invalid");
        }
    }

    public function getPage() {
        return isset($this->webRequest["page"]) ? $this->webRequest["page"] : 1;
    }

    public function getSearchType() {
        $searchTypes = array_intersect(self::SEARCH_TYPES, array_keys((array)$this->webRequest));
        return array_pop($searchTypes);
    }

    public function getMatch() {
        $searchType = $this->getSearchType();
        if ("term" === $searchType) {
            return $this->getTerm();
        } else if ("tag" === $searchType) {
            return $this->getTag() ? $this->getTag()->getName() : $this->webRequest["tag"];
        } else if ("category" === $searchType) {
            return $this->getCategory() ? $this->getCategory()->getName() : $this->webRequest["category"];
        } else if ("source" === $searchType) {
            return $this->getSource() ? $this->getSource()->getName() : $this->webRequest["source"];
        }
        return "Unknown";
    }
}
