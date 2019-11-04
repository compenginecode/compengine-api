<?php

namespace DomainLayer\ORM\Contributor\Repository;

use Doctrine\ORM\EntityRepository;
use DomainLayer\ORM\Contributor\Contributor;

/**
 * Class DatabaseContributorRepository
 * @package DomainLayer\ORM\Contributor\Repository
 */
class DatabaseContributorRepository

	extends EntityRepository
	implements IContributorRepository{

	/** findByEmailAddress
	 *
	 * 	Returns a Contributor matching the $emailAddress exactly.
	 *
	 * @param string $emailAddress
	 * @return Contributor|NULL
	 */
	public function findByEmailAddress($emailAddress){
		return $this->findOneBy(["emailAddress" => $emailAddress]);
	}

}