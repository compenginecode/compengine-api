<?php

namespace DomainLayer\ORM\Source\Repository;

use DomainLayer\ORM\Source\Source;

/**
 * Interface ISourceRepository
 * @package DomainLayer\ORM\Source\Repository
 */
interface ISourceRepository {

	/** findByKeyword
	 *
	 * 	Returns a Source matching the $keyword as a substring.
	 *
	 * @param string $keyword
	 * @return Source|NULL
	 */
	public function findByKeyword($keyword);

	/** $name
	 *
	 * 	Returns a Source matching the $name.
	 *
	 * @param string $name
	 * @return Source|NULL
	 */
	public function findOneByName($name);

	/** findByNameOrCreate
	 *
	 * 	Returns the Source identified by the $name. If there is no source found,
	 * 	a new source with that name will be returned.
	 *
	 * @param $name
     * @param $autoApprove = FALSE
	 * @return Source
	 */
	public function findByNameOrCreate($name, $autoApprove = FALSE);

	/** getRandomSource
	 *
	 * 	Returns a random source. This is for testing purposes so does not
	 * 	have to be highly performant.
	 *
	 * @return Source
	 */
	public function getRandomSource();

    /** listAll
     *
     *  Returns a list of all sources with counts.
     *
     * @return array
     */
    public function listAll();

    /** findBySlug
     *
     *
     *
     * @param string $slug
     * @return Source|null
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

    /** find
     *
     *
     *
     * @param $id
     * @return Source|null
     */
    public function find($id);

}