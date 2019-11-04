<?php

namespace DomainLayer\ORM\FeatureVectorFamily\Repository;

use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;

/**
 * Interface IFeatureVectorFamilyRepository
 * @package DomainLayer\ORM\FeatureVectorFamily\Repository
 */
interface IFeatureVectorFamilyRepository {

	/** findById
	 *
	 * 	Returns a FeatureVectorFamily matching the $id exactly.
	 *
	 * @param string $id
	 * @return FeatureVectorFamily|NULL
	 */
	public function findById($id);

}