<?php

namespace PresentationLayer\Routes\Admin\TimeSeries\Moderation\Get;

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
 * @package PresentationLayer\Routes\Admin\TimeSeries\Moderation\Get
 */
class Get extends UserInferredRoute
{

	protected function getSearchSQL(){
		$pageSize = 10;
		$page = isset($_GET["page"]) && !empty($_GET["page"]) ? $_GET["page"] : 1;
		$offset = ($page >= 1 ? $page - 1 : 0) * $pageSize;
	}

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
        $query = $this->entityManager->createQueryBuilder()
			->select("ts.id, 
				(CASE WHEN ts.isApproved = 1 THEN 'approved' ELSE (CASE WHEN ts.isRejected = 1 THEN 'rejected' ELSE 'pending' END) END) AS status,
				(CASE WHEN ts.isApproved = 1 THEN 2 ELSE (CASE WHEN ts.isRejected = 1 THEN 3 ELSE 1 END) END) AS statusOrder")
			->from(PersistedTimeSeries::class, 'ts');

        $query = $this->filterQuery($query);
        $countQuery = clone $query;
        $total = $countQuery->select("COUNT(ts.id)")->getQuery()->getSingleScalarResult();
        $query->setMaxResults($pageSize)->setFirstResult($offset);
        $this->sortQuery($query);
        $items = $query->getQuery()->execute();

        $entityManager = $this->entityManager;
        $items = array_map(function ($data) use ($entityManager) {
        	$timeSeries = $entityManager->getRepository(PersistedTimeSeries::class)->findOneBy(['id' => $data['id']]);

            return [
                "id" => $timeSeries->getId(),
                "name" => $timeSeries->getName(),
                "contributor" => $timeSeries->getContributor() ? $timeSeries->getContributor()->getName() : null,
                "description" => $timeSeries->getDescription(),
                "category" => $timeSeries->getCategory()->getName(),
                "source" => $timeSeries->getSource() ? $timeSeries->getSource()->getName() : null,
                "tags" => $timeSeries->getTagNames(),
				"status" => $data['status'],
                "size" => strlen(implode(",", $timeSeries->getDataPoints())),
                "samplingRate" => $timeSeries->getSamplingInformation() ? $timeSeries->getSamplingInformation()->getSamplingRate() : null,
                "samplingUnit" => $timeSeries->getSamplingInformation() ? $timeSeries->getSamplingInformation()->getSamplingUnit() : null,
                "timestampForSorting" => $timeSeries->timestampCreated()->format('U'),
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
            $query->leftJoin('ts.dataPoints', 'd')->orderBy("LENGTH(d.dataPoints)", $direction);
        } elseif ('status' === $field){
        	$query->orderBy('statusOrder', $direction);
		}
    }

    public function filterQuery(QueryBuilder $query) {
        if (empty($_GET["searchText"])) {
            return $query;
        }

        $searchText = $_GET["searchText"];

        $filterExpressions = [];

        $filterExpressions[] = 'ts.name LIKE :name';
        $query->setParameter('name', "%$searchText%");

        $filterExpressions[] = 'ts.description LIKE :description';
        $query->setParameter('description', "%$searchText%");

        $filterExpressions[] = "ts.samplingInformation.samplingRate = :samplingRate";
        $query->setParameter("samplingRate", $searchText);

        $filterExpressions[] = "ts.samplingInformation.samplingUnit = :samplingUnit";
        $query->setParameter("samplingUnit", $searchText);

		$filterExpressions[] = "(CASE WHEN ts.isApproved = 1 THEN 'approved' ELSE (CASE WHEN ts.isRejected = 1 THEN 'rejected' ELSE 'pending' END) END) = :pStatus";
		$query->setParameter("pStatus", strtolower($searchText));

        $query->leftJoin("ts.source", "fs");
        $filterExpressions[] = "fs.name LIKE :source";
        $query->setParameter("source", "%$searchText%");

        $query->leftJoin('ts.category', 'fc');
        $filterExpressions[] = "fc.name LIKE :category";
        $query->setParameter("category", "%$searchText%");

        $contributors = $this->entityManager->getRepository(Contributor::class)->createQueryBuilder("c")->where("c.name LIKE :name")
            ->setParameter("name", "%$searchText%")->getQuery()->execute();

        $contributorExpressions = array_map(function (Contributor $contributor) use ($query) {
            $query->setParameter("contributor{$contributor->getId()}", $contributor);
            return "ts.contributor = :contributor{$contributor->getId()}";
        }, $contributors);

        $filterExpressions = array_merge($filterExpressions, $contributorExpressions);

        $tags = $this->entityManager->getRepository(Tag::class)->createQueryBuilder("t")->where("t.name LIKE :name")
            ->setParameter("name", "%$searchText%")->getQuery()->execute();

        array_walk($tags, function ($tag, $index) use ($query, &$filterExpressions) {
            if (null !== $tag) {
                $filterExpressions[] = ":tag$index MEMBER OF ts.tags";
                $query->setParameter("tag$index", $tag);
            }
        });

        $query->andWhere(join(" OR ", $filterExpressions));

        return $query;
    }

}
