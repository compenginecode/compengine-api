<?php

namespace DomainLayer\ORM\FeatureVectorFamily\Repository;

use Doctrine\ORM\EntityRepository;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;

/**
 * Class DatabaseFeatureVectorFamilyRepository
 * @package DomainLayer\ORM\FeatureVectorFamily\Repository
 */
class DatabaseFeatureVectorFamilyRepository

	extends EntityRepository
	implements IFeatureVectorFamilyRepository{

	/** findByKeyword
	 *
	 * 	Returns a FeatureVectorFamily matching the $id exactly.
	 *
	 * @param string $id
	 * @return FeatureVectorFamily|NULL
	 */
	public function findById($id){
		return $this->findOneBy(["id" => $id]);
	}

}