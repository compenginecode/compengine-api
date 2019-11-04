<?php

namespace DomainLayer\ORM\Hyperplane;

use DomainLayer\Common\Vector\Vector;
use DomainLayer\ORM\DomainEntity\DomainEntity;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\ORM\HashTable\HashTable;

/**
 * Class Hyperplane
 * @package DomainLayer\ORM\Hyperplane
 */
class Hyperplane extends DomainEntity{

	/** $partitionVector
	 *
	 * 	The partition vector (the plane itself). Note that this
	 * 	variable stores the serialized vector.
	 *
	 * @var string
	 */
	protected $partitionVector;

	/** $family
	 *
	 * 	The feature vector family that manages this hyperplane.
	 *
	 * @var FeatureVectorFamily
	 */
	protected $hashTable;

	/** sign
	 *
	 * 	Binary sign function. Returns 1 if the variable is positive and
	 * 	0, otherwise.
	 *
	 * @param $v
	 * @return int
	 */
	protected function sign($v){
		return ($v > 0 ? 1 : 0);
	}

	/** generateGaussian
	 *
	 * 	Generates and returns a random Gaussian number.
	 *
	 * @return float
	 */
	protected function generateGaussian(){
		$rand1 = (float)mt_rand()/(float)mt_getrandmax();
		$rand2 = (float)mt_rand()/(float)mt_getrandmax();
		$gaussianNumber = sqrt(-2 * log($rand1)) * cos(2 * M_PI * $rand2);
		return $gaussianNumber;
	}

	/** generateRandomVector
	 *
	 * 	Generates and returns a vector with random, Gaussian distributed elements
	 * 	of dimension $spacialDimension.
	 *
	 * @param int $spacialDimension
	 * @return Vector
	 */
	protected function generateRandomVector($spacialDimension){
		$vecArray = [];
		for($i = 0; $i < $spacialDimension; $i++){
			$vecArray[] = $this->generateGaussian();
		}

		return Vector::fromArray($vecArray);
	}

	/** setPartitionVector
	 *
	 * 	Sets the partition vector of the hyperplane.
	 *
	 * @param Vector $partitionVector
	 */
	protected function setPartitionVector(Vector $partitionVector){
		/** We serialize it so Doctrine can persist it */
		$this->partitionVector = $partitionVector->serialize();
	}

	/** __construct
	 *
	 * 	Hyperplane constructor.
	 *
	 * @param HashTable $hashTable
	 * @param $spacialDimension
	 */
	public function __construct(HashTable $hashTable, $spacialDimension){
		$this->hashTable = $hashTable;
		$this->partitionVector = $this->generateRandomVector($spacialDimension)->serialize();
	}

	/** getPartitionVector
	 *
	 * 	Returns the partition vector of the hyperplane.
	 *
	 * @return Vector
	 */
	public function getPartitionVector() {
		return Vector::unserialize($this->partitionVector);
	}

	/** hash
	 *
	 * 	Hashes the vector with the partition vector using the standard inner product,
	 * 	and returns either 0 or 1 according to the sign.
	 *
	 * @param Vector $vector
	 * @return int
	 * @throws \Exception
	 */
	public function hash(Vector $vector){
		return $this->sign($vector->dot($this->getPartitionVector()));
	}

}