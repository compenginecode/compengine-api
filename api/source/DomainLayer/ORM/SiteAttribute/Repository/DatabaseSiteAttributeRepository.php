<?php

namespace DomainLayer\ORM\SiteAttribute\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;

/**
 * Class DatabaseSiteAttributeRepository
 * @package DomainLayer\ORM\SiteAttribute\Repository
 *
 * 	We have to leave this class mainly empty, and instead, use composition over
 * 	inheritance to get the desired behavior we are after. We want to be able to
 * 	modify the constructor, but we cannot as Doctrine controls instantiation of
 * 	repositories, not our DI container.
 *
 */
class DatabaseSiteAttributeRepository extends EntityRepository{

	/** em
	 *
	 * 	Returns the entity manager.
	 *
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function em(){
		return $this->_em;
	}

}