<?php

namespace PresentationLayer\Routes\Admin\TimeSeries\Search\Get;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use DomainLayer\ORM\Contributor\Contributor;
use DomainLayer\ORM\Tag\Tag;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\Admin\AdminRoute;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;
use Carbon\Carbon;

/**
 * Class Get
 * @package PresentationLayer\Routes\Admin\TimeSeries\Search\Get
 */
class Get extends UserInferredRoute
{

    /** __construct
     *
     *  Constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager) {
		parent::__construct($sessionService, $entityManager);
    }

    public function execute() {
		parent::execute();

		$timeSeriesRepository = $this->entityManager->getRepository(PersistedTimeSeries::class);

        $page = isset($_GET["page"]) && !empty($_GET["page"]) ? $_GET["page"] : 1;

        $pageSize = 10;
        $offset = ($page >= 1 ? $page - 1 : 0) * $pageSize;
        $query = $timeSeries = $timeSeriesRepository->createQueryBuilder("ts");
        $query = $this->filterQuery($query);
        $countQuery = clone $query;
        $total = $countQuery->select("COUNT(ts.id)")->getQuery()->getSingleScalarResult();
        $query->setMaxResults($pageSize)->setFirstResult($offset);
        $this->sortQuery($query);
        $items = $query->getQuery()->execute();

        $items = array_map(function (PersistedTimeSeries $timeSeries) {
            return [
                "id" => $timeSeries->getId(),
                "name" => $timeSeries->getName(),
                "contributor" => $timeSeries->getContributor() ? $timeSeries->getContributor()->getName() : null,
                "description" => $timeSeries->getDescription(),
                "category" => $timeSeries->getCategory()->getName(),
                "source" => $timeSeries->getSource() ? $timeSeries->getSource()->getName() : null,
                "tags" => $timeSeries->getTagNames(),
                "size" => strlen(implode(",", $timeSeries->getDataPoints())),
                "samplingRate" => $timeSeries->getSamplingInformation() ? $timeSeries->getSamplingInformation()->getSamplingRate() : null,
                "samplingUnit" => $timeSeries->getSamplingInformation() ? $timeSeries->getSamplingInformation()->getSamplingUnit() : null,
	            "timestampForSorting" => $timeSeries->timestampCreated(),
                "timestampCreated" => Carbon::createFromTimestamp($timeSeries->timestampCreated()->getTimestamp())->diffForHumans()
            ];
        }, $items);

        $this->response->setReturnBody(new JSONBody(compact("items", "total", "pageSize", "page")));
    }

    public function sortQuery(QueryBuilder $query) {
        $direction = !empty($_GET['sortByDirection']) && "DESC" === $_GET['sortByDirection'] ? "DESC" : "ASC";

        if (empty($_GET["sortByField"])) {
            return;
        }

        $field = $_GET["sortByField"];

        if ("id" === $field) {
            $query->orderBy("ts.id", $direction);
        } elseif ("name" === $field) {
            $query->orderBy("ts.name", $direction);
        } elseif ("category" === $field) {
            $query->leftJoin('ts.category', 'c')
                ->orderBy("c.name", $direction);
        } elseif ("contributor" === $field) {
            $query->leftJoin('ts.contributor', 'c')
                ->orderBy("c.name", $direction);
        } elseif ("source" === $field) {
            $query->leftJoin('ts.source', 's')
                ->orderBy("s.name", $direction);
        } elseif ("samplingRate" === $field) {
            $query->orderBy("ts.samplingInformation.samplingRate", $direction);
        } elseif ("samplingUnit" === $field) {
            $query->orderBy("ts.samplingInformation.samplingUnit", $direction);
        } elseif ("timestampCreated" === $field) {
            $query->orderBy("ts.timestampCreated", $direction);
        } elseif ("size" === $field) {
            $query->leftJoin('ts.dataPoints', 'd')
                ->orderBy("LENGTH(d.dataPoints)", $direction);
        }
    }

    /**
     * filterQuery
     *
     *
     *
     * @param QueryBuilder $query
     * @return QueryBuilder
     */
    public function filterQuery(QueryBuilder $query) {
        if (isset($_GET["name"])) {
            $query->andWhere("ts.name LIKE :name")
                ->setParameter("name", "%$_GET[name]%");
        }

        if (isset($_GET["description"])) {
            $query->andWhere("ts.description LIKE :description")
                ->setParameter("description", "%$_GET[description]%");
        }

        if (isset($_GET["samplingRate"])) {
            $query->andWhere("ts.samplingInformation.samplingRate = :samplingRate")
                ->setParameter("samplingRate", $_GET["samplingRate"]);
        }

        if (isset($_GET["samplingUnit"])) {
            $query->andWhere("ts.samplingInformation.samplingUnit = :samplingUnit")
                ->setParameter("samplingUnit", $_GET["samplingUnit"]);
        }

        if (isset($_GET["source"]) && !empty($_GET["source"])) {
            $query->andWhere("ts.source = :source")
                ->setParameter("source", $_GET["source"]);
        }

        if (isset($_GET["category"]) && !empty($_GET["category"])) {
            $query->andWhere("ts.category = :category")
                ->setParameter("category", $_GET["category"]);
        }

        if (isset($_GET["contributor"]) && !empty($_GET["contributor"])) {
            $contributors = $this->entityManager->getRepository(Contributor::class)->createQueryBuilder("c")->where("c.name LIKE :name")
                ->setParameter("name", "%$_GET[contributor]%")->getQuery()->execute();

            $contributorExpressions = array_map(function (Contributor $contributor) use ($query) {
                $query->setParameter("contributor{$contributor->getId()}", $contributor);
                return "ts.contributor = :contributor{$contributor->getId()}";
            }, $contributors);
            if ($contributorExpressions) {
                $query->andWhere(implode(" OR ", $contributorExpressions));
            } else {
                // user entered a contributor name but it didn't match any known contributors so no results should be returned
                $query->andWhere("1=0");
            }
        }

        if (isset($_GET["startDate"]) && !empty($_GET["startDate"])) {
            $query->andWhere(":startDate <= ts.timestampCreated")
                ->setParameter("startDate", $_GET["startDate"]);
        }

        if (isset($_GET["endDate"]) && !empty($_GET["endDate"])) {
            $query->andWhere("ts.timestampCreated <= :endDate")
                ->setParameter("endDate", $_GET["endDate"] . " 23:59:59");
        }

        if (isset($_GET["tags"]) && is_array($_GET["tags"])) {
            $tags = array_map(function ($tagName) {
                return $this->entityManager->getRepository(Tag::class)->findBy(["name" => $tagName]);
            }, $_GET["tags"]);
            array_walk($tags, function ($tag, $index) use ($query) {
                if (null !== $tag) {
                    $query->andWhere(":tag$index MEMBER OF ts.tags")
                        ->setParameter("tag$index", $tag);
                }
            });
        }

        return $query;
    }
}
