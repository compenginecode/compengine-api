<?php

namespace DomainLayer\ORM\Category\Repository;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;

/**
 * Class DatabaseCategoryRepository
 * @package DomainLayer\ORM\Category\Repository
 */
class DatabaseCategoryRepository

	extends EntityRepository
	implements ICategoryRepository{

    const PAGE_SIZE = 10;

	/** findById
	 *
	 * 	Returns a Category matching the given $id, or NULL otherwise.
	 *
	 * @param $id
	 * @return Category|NULL
	 */
	public function findById($id){
		return $this->findOneBy(["id" => $id]);
	}

	/** getRootCategories
	 *
	 * 	Returns all root categories (categories with no parents).
	 *
	 * @return array
	 */
	public function getRootCategories(){
		return $this->_em->createQueryBuilder()
			->select("category")
			->from("DomainLayer\\ORM\\Category\\Category", "category")
			->where("category.parent IS NULL")
            ->andWhere("category.approvalStatus.value = :pApprovalStatus")
            ->setParameter(":pApprovalStatus", "approved")
			->getQuery()
			->getResult();
	}

    /** findByNameOrCreate
     *
     * 	Returns the Category identified by the $name. If there is no category found,
     * 	a new category with that name will be returned.
     *
     * @param $name
     * @param $autoApprove
     * @return Category
     */
    public function findByNameOrCreate($name, $autoApprove = FALSE){
        $categoryObject = $this->findOneBy(["name" => $name]);
        if (NULL === $categoryObject){
            $categoryObject = new Category($name, $this->getRootCategories()[0]);

            if ($autoApprove){
                $categoryObject->setApprovalStatus(ApprovalStatus::approved());
            }
        }

        return $categoryObject;
    }


	/** getRandomCategory
	 *
	 * 	Returns a random category. This is for testing purposes so does not
	 * 	have to be highly performant.
	 *
	 * @return Category
	 */
	public function getRandomCategory(){
		$categories = $this->findAll();
		return $categories[array_rand($categories, 1)];
	}

    /** listAll
     *
     *  Return a recursive list of all root categories and their children
     *
     * @return array
     */
    public function listAll() {
        return $this->getCategoriesRecursive();
	}

    /** getCategoryRecursive
     *
     *
     *
     * @param Category|null $parent
     * @return array
     */
    private function getCategoriesRecursive($parent = NULL) {
        $query = $this->_em->getRepository(Category::class)->createQueryBuilder("c")
            ->select("c.id", "c.name", "c.slug", "COUNT(p.id) AS total")
            ->leftJoin("c.persistedTimeSeries", "p");

        if ($parent) {
            $query = $query->where("c.parent = :parent")->setParameter("parent", $parent);
        } else {
            $query = $query->where("c.parent IS NULL");
        }

        $categories = $query
	        ->andWhere($this->_em->getRepository(Category::class)->createQueryBuilder("c")->expr()->neq("c.approvalStatus.value", ":pUnapproved"))
	        ->setParameter("pUnapproved", "unapproved")
	        ->groupBy("c.id")
            ->orderBy("c.name")
	        ->getQuery()->execute();

        return array_map(function ($category) {
            $category["total"] = (int) $category["total"];
            $category["children"] = $this->getCategoriesRecursive($category["id"]);
            return $category;
        }, $categories);
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

    /** paginate
     *
     *
     *
     * @param int $page
     * @return array
     */
    public function paginate($page = 1) {
        $pageSize = self::PAGE_SIZE;
        $offset = ($page >= 1 ? $page - 1 : 0) * $pageSize;
        $query = $this->_em->getRepository(Category::class)->createQueryBuilder("c")
            ->select("c.id", "c.name", "c.slug");
        $countQuery = clone $query;
        $total = $countQuery->select("COUNT(c.id)")->getQuery()->getSingleScalarResult();
        $query->setMaxResults($pageSize)->setFirstResult($offset);
        $items = $query->getQuery()->execute();

        return compact("items", "pageSize", "total", "page");
    }

    /** findByName
     *
     *
     *
     * @param $name
     * @return Category|null
     */
    public function findByName($name) {
        /** @var Category $category */
        $category = $this->findOneBy(compact("name"));
        return $category;
    }

    /** adminGetCategoryRecursive
     *
     *
     *
     * @param Category|null $parent
     * @return array
     */
    private function adminGetCategoriesRecursive($parent = NULL) {
        $query = $this->_em->getRepository(Category::class)->createQueryBuilder("c")
            ->select("c.id", "c.name", "c.slug", "c.approvalStatus.value approvalStatus", "COUNT(p.id) AS total")
            ->leftJoin("c.persistedTimeSeries", "p");

        if ($parent) {
            $query = $query->where("c.parent = :parent")->setParameter("parent", $parent);
        } else {
            $query = $query->where("c.parent IS NULL");
        }

        $categories = $query
            ->groupBy("c.id")
            ->orderBy("c.name")
            ->getQuery()->execute();

        return array_map(function ($category) use ($parent) {
            $children = $this->adminGetCategoriesRecursive($category["id"]);
            $category["total"] = $category["total"] + array_reduce($children, function ($carry, $child) { return $carry + $child["total"]; }, 0);
            $category["parentId"] = $parent;
            $category["children"] = $children;
            return $category;
        }, $categories);
    }

    /** adminListAll
     *
     *  Return a recursive list of all root categories and their children
     *
     * @return array
     */
    public function adminListAll() {
        return $this->adminGetCategoriesRecursive();
    }

    public function adminSearchAll($name) {
        $query = $this->_em->getRepository(Category::class)->createQueryBuilder("c")
            ->select("c.id", "c.name", "c.slug", "c.approvalStatus.value approvalStatus")
            ->where("c.name like :name")->setParameter("name", "%" . $name . "%");

        return $query
            ->orderBy("c.name")
            ->setMaxResults(10)
            ->getQuery()->execute();
    }

}