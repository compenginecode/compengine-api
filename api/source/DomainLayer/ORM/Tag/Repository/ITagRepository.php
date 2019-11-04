<?php

namespace DomainLayer\ORM\Tag\Repository;

use DomainLayer\ORM\Tag\Tag;

/**
 * Interface ITagRepository
 * @package DomainLayer\ORM\Tag\Repository
 */
interface ITagRepository {

	/** findByKeyword
	 *
	 * 	Returns a Tag matching the $keyword as a substring.
	 *
	 * @param string $keyword
	 * @return Tag|NULL
	 */
	public function findByKeyword($keyword);

	/** findByNameOrCreate
	 *
	 * 	Returns the Tag identified by the $name. If there is no tag found,
	 * 	a new source with that name will be returned.
	 *
	 * @param $name
     * @param $autoApprove = FALSE
	 * @return Tag
	 */
	public function findByNameOrCreate($name, $autoApprove = FALSE);

	/** getRandomArrayOfTags
	 *
	 * 	Returns a random array of tags. This is for testing purposes so does not
	 * 	have to be highly performant.
	 *
	 * @param int $max
	 * @return array
	 */
	public function getRandomArrayOfTags($max);

    /** listAll
     *
     *  Return list of all tags with counts.
     *
     * @return array
     */
    public function listAll();

    /** findBySlug
     *
     *
     *
     * @param string $slug
     * @return Tag|null
     */
    public function findBySlug($slug);

    /** paginate
     *
     *
     *
     * @param $page
     * @return array
     */
    public function paginate($page = 1);

    /** find
     *
     *
     *
     * @param $id
     * @return Tag|null
     */
    public function find($id);

    /** findByName
     *
     *
     *
     * @param $name
     * @return Tag|null
     */
    public function findByName($name);
}