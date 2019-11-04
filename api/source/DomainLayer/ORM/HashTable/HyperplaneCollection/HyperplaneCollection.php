<?php

namespace DomainLayer\ORM\HashTable\HyperplaneCollection;

use DomainLayer\Common\Collection\Collection;
use DomainLayer\ORM\HashTable\HashTable;
use DomainLayer\ORM\Hyperplane\Hyperplane;

/**
 * Class HyperplaneCollection
 * @package DomainLayer\ORM\HashTable\HyperplaneCollection
 */
class HyperplaneCollection extends Collection{

	/** getParentFamily
	 *
	 * 	Returns the parent hash table.
	 *
	 * @return HashTable
	 */
	public function getParentHashTable(){
		return $this->owner;
	}

	/** addHyperplane
	 *
	 * 	Adds and returns a new hyperplane.
	 *
	 * @return Hyperplane
	 */
	public function addHyperplane($numberOfDescriptors){
		$hyperplane = new Hyperplane(
			$this->getParentHashTable(),
			$numberOfDescriptors
		);

		$this->arrayCollection->add($hyperplane);

		return $hyperplane;
	}

}