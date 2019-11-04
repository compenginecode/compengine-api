<?php

namespace DomainLayer\ORM\Contributor\Repository;

use DomainLayer\ORM\Contributor\Contributor;

/**
 * Interface IContributorRepository
 * @package DomainLayer\ORM\Contributor\Repository
 */
interface IContributorRepository {

	/** findByEmailAddress
	 *
	 * 	Returns a Contributor matching the $emailAddress exactly.
	 *
	 * @param string $emailAddress
	 * @return Contributor|NULL
	 */
	public function findByEmailAddress($emailAddress);

}