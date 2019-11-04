<?php

namespace DomainLayer\ORM\Source\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\Source\Source;

/**
 * Class DatabaseSourceRepository
 * @package DomainLayer\ORM\Source\Repository
 */
class DatabaseSourceRepository

	extends EntityRepository
	implements ISourceRepository{

    const PAGE_SIZE = 10;

    /** findByKeyword
     *
     * 	Returns a Source matching the $keyword as a substring.
     *
     * @param string $keyword
     * @return array
     */
    public function findByKeyword($keyword){
        return $this->_em->createQueryBuilder()
            ->select("source")
            ->from("DomainLayer\\ORM\\Source\\Source", "source")
            ->where("source.name LIKE :pName")
            ->setParameter(":pName", "%$keyword%")
            ->andWhere("source.approvalStatus.value = :pApprovalStatus")
            ->setParameter(":pApprovalStatus", "approved")
            ->getQuery()
            ->getResult();
    }

	/** $name
	 *
	 * 	Returns a Source matching the $name.
	 *
	 * @param string $name
	 * @return Source|NULL
	 */
	public function findOneByName($name){
		return $this->findOneBy(["name" => $name]);
	}

	/** findByNameOrCreate
	 *
	 * 	Returns the Source identified by the $name. If there is no source found,
	 * 	a new source with that name will be returned.
	 *
	 * @param $name
     * @param $autoApprove = FALSE
	 * @return Source
	 */
	public function findByNameOrCreate($name, $autoApprove = FALSE){
		$sourceObject = $this->findOneBy(["name" => $name]);
		if (NULL === $sourceObject){
			$sourceObject = new Source($name);

            if ($autoApprove){
                $sourceObject->setApprovalStatus(ApprovalStatus::approved());
            }
		}

		return $sourceObject;
	}

	/** getRandomSource
	 *
	 * 	Returns a random source. This is for testing purposes so does not
	 * 	have to be highly performant.
	 *
	 * @return Source
	 */
	public function getRandomSource(){
		$sources = $this->findAll();
		return $sources[array_rand($sources, 1)];
	}

    /** listAll
     *
     *  Returns a list of all sources with counts.
     *
     * @return array
     */
    public function listAll() {
        return $this->_em->getRepository(Source::class)->createQueryBuilder("s")
            ->select("s.id, s.name", "s.slug", "COUNT(p.id) AS total")
            ->leftJoin("s.persistedTimeSeries", "p")
	        ->where($this->_em->getRepository(Source::class)->createQueryBuilder("s")->expr()->neq("s.approvalStatus.value", ":pUnapproved"))
            ->groupBy("s.id")
	        ->setParameter("pUnapproved", "unapproved")
            ->getQuery()->execute();
	}

    /** findBySlug
     *
     *
     *
     * @param string $slug
     * @return null|object
     */
    public function findBySlug($slug) {
        return $this->findOneBy(compact("slug"));
    }

    public function paginate($page = 1) {
        $pageSize = self::PAGE_SIZE;
        $offset = ($page >= 1 ? $page - 1 : 0) * $pageSize;
        $query = $this->_em->getRepository(Source::class)->createQueryBuilder("s")
            ->select("s.id", "s.name", "s.slug", "s.approvalStatus.value approvalStatus");
        $this->filterQuery($query);
        $countQuery = clone $query;
        $total = $countQuery->select("COUNT(s.id)")->getQuery()->getSingleScalarResult();
        $query
            ->addSelect("COUNT(p.id) AS total")
            ->leftJoin("s.persistedTimeSeries", "p")
            ->groupBy("s.id")
            ->setMaxResults($pageSize)->setFirstResult($offset);
        $this->sortQuery($query);
        $items = $query->getQuery()->execute();
        $items = array_map(function ($item) {
            $item["total"] = (int) $item["total"];
            return $item;
        }, $items);

        return compact("items", "pageSize", "total", "page");
    }

    private function filterQuery(QueryBuilder $query) {
        if (empty($_GET["searchText"])) {
            return $query;
        }

        $searchText = $_GET["searchText"];

        $filterExpressions = [];

        $filterExpressions[] = 's.name LIKE :name';
        $query->setParameter('name', "%$searchText%");

        $query->andWhere(join(" OR ", $filterExpressions));

        return $query;
    }

    private function sortQuery(QueryBuilder $query) {
        $query->addOrderBy("s.approvalStatus.value", "DESC");

        $direction = !empty($_GET['sortByDirection']) && "DESC" === $_GET['sortByDirection'] ? "DESC" : "ASC";

        if (empty($_GET["sortByField"])) {
            return;
        }

        $field = $_GET["sortByField"];

        if ("name" === $field) {
            $query->addOrderBy("s.name", $direction);
        } elseif ("total" === $field) {
            $query->addOrderBy("total", $direction);
        } elseif ("id" === $field) {
            $query->addOrderBy("s.id", $direction);
        }
    }

}