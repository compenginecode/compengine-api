<?php

namespace Tests\UnitTests\DomainLayer\ORM\HyperplaneCollection;

use Doctrine\Common\Collections\ArrayCollection;
use DomainLayer\ORM\HashTable\HyperplaneCollection\HyperplaneCollection;

/**
 * Class HyperplaneCollection_Test
 * @package Tests\UnitTests\DomainLayer\ORM\HyperplaneCollection
 */
class HyperplaneCollection_Test extends \PHPUnit_Framework_TestCase {

	/** test_can_instantiate_class
	 *
	 * 	Ensures that the class can be instantiated.
	 *
	 */
	public function test_can_instantiate_class(){
		$fvf = \Mockery::mock("DomainLayer\\ORM\\FeatureVectorFamily\\FeatureVectorFamily");
		$instance = new HyperplaneCollection(new ArrayCollection(), $fvf);
		$this->assertNotNull($instance);
	}

}