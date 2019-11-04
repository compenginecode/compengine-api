<?php

namespace DomainLayer\SearchService;

use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository\DatabaseTimeSeriesRepository;
use InfrastructureLayer\ElasticSearch\ElasticSearch;

/**
 * Class SearchService
 * @package DomainLayer\SearchService
 */
class SearchService
{
    const PAGE_SIZE = 10;

    /** timeSeriesRepository
     *
     *
     *
     * @var DatabaseTimeSeriesRepository
     */
    private $timeSeriesRepository;

    /** elasticSearch
     *
     *
     *
     * @var ElasticSearch
     */
    private $elasticSearch;

    /** siteAttributeRepository
     *
     *
     *
     * @var ISiteAttributeRepository
     */
    private $siteAttributeRepository;

    /** __construct
     *
     *  Constructor
     *
     * @param DatabaseTimeSeriesRepository $timeSeriesRepository
     * @param ElasticSearch $elasticSearch
     * @param ISiteAttributeRepository $siteAttributeRepository
     */
    public function __construct(DatabaseTimeSeriesRepository $timeSeriesRepository, ElasticSearch $elasticSearch, ISiteAttributeRepository $siteAttributeRepository) {
        $this->timeSeriesRepository = $timeSeriesRepository;
        $this->elasticSearch = $elasticSearch;
        $this->siteAttributeRepository = $siteAttributeRepository;
    }

    /** search
     *
     *
     *
     * @param ISearchRequest $request
     * @return array
     */
    public function search(ISearchRequest $request) {
        $pageSize = self::PAGE_SIZE;
        $page = $request->getPage();
        $offset = ($page >= 1 ? $page - 1 : 0) * $pageSize;
        $query = $this->timeSeriesRepository->createQueryBuilder("ts");

		if ($request->hasTerm()) {
		    $elasticSearchResults = $this->elasticSearch->search(
			    trim($request->getTerm()),
			    $this->siteAttributeRepository->getCurrentFeatureVectorFamily()->getIndexName(),
			    $offset,
			    $pageSize
		    );
		    $ids = array_map(function ($hit) {
			    return $hit["_source"]["timeSeriesId"];
		    }, $elasticSearchResults["hits"]["hits"]);
		    if (0 === count($ids)) {
			    return ["total" => 0, "page" => $page, "pageSize" => $pageSize, "items" => []];
		    }
		    $query->where($query->expr()->in("ts.id", $ids));
			$total = $elasticSearchResults["hits"]["total"];
	    } else {
			if ($request->hasCategory()) {
			    $categoryIds = array_merge([$request->getCategory()->getId()], $this->getRecursiveChildIdsForCategory($request->getCategory()));
				$query->andWhere("ts.category IN (:categoryIds)")->setParameter("categoryIds",$categoryIds);
			} else if ($request->hasTag()) {
				$query->innerJoin("ts.tags", "t")->where("t = :tag")->setParameter("tag", $request->getTag());
			} else if ($request->hasSource()) {
				$query->where("ts.source = :source")->setParameter("source", $request->getSource());
			}
			$countQuery = clone $query;
			$total = $countQuery->select("COUNT(ts.id)")->getQuery()->getSingleScalarResult();
			$query->setMaxResults($pageSize)->setFirstResult($offset);
		}

	    $items = $query->getQuery()->execute();
        return compact("total", "page", "pageSize", "items");
    }

    /**
     * @param Category $category
     * @return array
     */
    private function getRecursiveChildIdsForCategory(Category $category)
    {
        $immediateChildrenIds = array_map(function (Category $category) {
            return $category->getId();
        }, $category->getChildren()->toArray());

        return array_reduce($category->getChildren()->toArray(), function ($carry, Category $category) {
            return array_merge($carry, $this->getRecursiveChildIdsForCategory($category));
        }, $immediateChildrenIds);
    }
}
