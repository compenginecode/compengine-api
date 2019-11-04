<?php

namespace DomainLayer\ORM\Category\Repository;

use Doctrine\Common\Collections\Collection;
use DomainLayer\ORM\Category\Category;

/**
 * Interface ICategoryRepository
 * @package DomainLayer\ORM\Category\Repository
 */
interface ICategoryRepository {

	/** findById
	 *
	 * 	Returns a Category matching the given $id, or NULL otherwise.
	 *
	 * @param $id
	 * @return Category|NULL
	 */
	public function findById($id);

	/** getRootCategories
	 *
	 * 	Returns all root categories (categories with no parents).
	 *
	 * @return Collection
	 */
	public function getRootCategories();

    /** findByNameOrCreate
     *
     * 	Returns the Category identified by the $name. If there is no category found,
     * 	a new category with that name will be returned.
     *
     * @param $name
     * @param $autoApprove = FALSE
     * @return Category
     */
    public function findByNameOrCreate($name, $autoApprove = FALSE);

	/** getRandomCategory
	 *
	 * 	Returns a random category. This is for testing purposes so does not
	 * 	have to be highly performant.
	 *
	 * @return Category
	 */
	public function getRandomCategory();

    /** listAll
     *
     *  Return a recursive list of all root categories and their children
     *
     * @return array
     */
    public function listAll();

    /** findBySlug
     *
     *
     *
     * @param string $slug
     * @return Category|null
     */
    public function findBySlug($slug);

    /** paginate
     *
     *
     *
     * @param int $page
     * @return mixed
     */
    public function paginate($page = 1);

    /** findByName
     *
     *
     *
     * @param $name
     * @return Category|null
     */
    public function findByName($name);

    /** adminListAll
     *
     *  Return a recursive list of all root categories and their children
     *
     * @return array
     */
    public function adminListAll();

    /** adminSearchAll
     *
     *  Return a list of all categories filtered by name
     *
     * @param string $name
     * @return array
     */
    public function adminSearchAll($name);

}