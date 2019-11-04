<?php

namespace DomainLayer\ORM\Tag\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\Tag\Tag;

/**
 * Class DatabaseTagRepository
 * @package DomainLayer\ORM\Tag\Repository
 */
class DatabaseTagRepository

	extends EntityRepository
	implements ITagRepository{

    const PAGE_SIZE = 10;

	/** findByKeyword
	 *
	 * 	Returns a Tag matching the $keyword as a substring.
	 *
	 * @param string $keyword
     * @return array
     */
    public function findByKeyword($keyword){
        return $this->_em->createQueryBuilder()
            ->select("tag")
            ->from("DomainLayer\\ORM\\Tag\\Tag", "tag")
            ->where("tag.name LIKE :pName")
            ->setParameter(":pName", "%$keyword%")
            ->andWhere("tag.approvalStatus.value = :pApprovalStatus")
            ->setParameter(":pApprovalStatus", "approved")
            ->getQuery()
            ->getResult();
    }

	/** findByNameOrCreate
	 *
	 * 	Returns the Tag identified by the $name. If there is no tag found,
	 * 	a new source with that name will be returned.
	 *
	 * @param $name
     * @param $autoApprove = FALSE
	 * @return Tag
	 */
	public function findByNameOrCreate($name, $autoApprove = FALSE){
		$tagObject = $this->findOneBy(["name" => $name]);
		if (NULL === $tagObject){
			$tagObject = new Tag($name);

            if ($autoApprove){
                $tagObject->setApprovalStatus(ApprovalStatus::approved());
            }
		}

		return $tagObject;
	}

    /** findByName
     *
     * 	Returns the Tag identified by the $name. If there is no tag found,
     * 	a new source with that name will be returned.
     *
     * @param $name
     * @return Tag
     */
    public function findByName($name){
        /** @var Tag $tag */
        $tag = $this->findOneBy(["name" => $name]);
        return $tag;
    }

	/** getRandomArrayOfTags
	 *
	 * 	Returns a random array of tags. This is for testing purposes so does not
	 * 	have to be highly performant.
	 *
	 * @param int $max
	 * @return array
	 */
	public function getRandomArrayOfTags($max){
		$tags = $this->findAll();
		$keys = array_rand($tags, $max);

		$tagsToReturn = [];
		foreach ($keys as $aKey) {
			$tagsToReturn[] = $tags[$aKey];
		}
		
		return $tagsToReturn;
	}

    /** listAll
     *
     *  Return list of all tags with counts.
     *
     * @return array
     */
    public function listAll() {
        $tags = $this->_em->getRepository(Tag::class)->createQueryBuilder("t")
            ->select("t.id", "t.name", "t.slug", "COUNT(p.id) AS total")
            ->leftJoin("t.persistedTimeSeries", "p")
	        ->where($this->_em->getRepository(Tag::class)->createQueryBuilder("t")->expr()->neq("t.approvalStatus.value", ":pUnapproved"))
            ->groupBy("t.id")
            ->orderBy("total", "DESC")
	        ->setParameter("pUnapproved", "unapproved")
            ->getQuery()->execute();

        return array_map(function ($tag) {
            $tag["total"] = (int) $tag["total"];
            return $tag;
        }, $tags);
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
        $query = $this->_em->getRepository(Tag::class)->createQueryBuilder("t")
            ->select("t.id", "t.name", "t.slug", "t.approvalStatus.value approvalStatus");
        $this->filterQuery($query);
        $countQuery = clone $query;
        $total = $countQuery->select("COUNT(t.id)")->getQuery()->getSingleScalarResult();
        $query
            ->addSelect("COUNT(p.id) AS total")
            ->leftJoin("t.persistedTimeSeries", "p")
            ->groupBy("t.id")
            ->setMaxResults($pageSize)->setFirstResult($offset);
        $this->sortQuery($query);
        $items = $query->getQuery()->execute();

        return compact("items", "pageSize", "total", "page");
    }

    private function filterQuery(QueryBuilder $query) {
        if (empty($_GET["searchText"])) {
            return $query;
        }

        $searchText = $_GET["searchText"];

        $filterExpressions = [];

        $filterExpressions[] = 't.name LIKE :name';
        $query->setParameter('name', "%$searchText%");

        $query->andWhere(join(" OR ", $filterExpressions));

        return $query;
    }

    private function sortQuery(QueryBuilder $query) {
        $query->addOrderBy("t.approvalStatus.value", "DESC");

        $direction = !empty($_GET['sortByDirection']) && "DESC" === $_GET['sortByDirection'] ? "DESC" : "ASC";

        if (empty($_GET["sortByField"])) {
            return;
        }

        $field = $_GET["sortByField"];

        if ("name" === $field) {
            $query->addOrderBy("t.name", $direction);
        } elseif ("total" === $field) {
            $query->addOrderBy("total", $direction);
        } elseif ("id" === $field) {
            $query->addOrderBy("t.id", $direction);
        }
    }

}