<?php

namespace DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository;

use Doctrine\ORM\EntityRepository;
use DomainLayer\Common\Collection\Collection;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;

/**
 * Class DatabaseTimeSeriesRepository
 * @package DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository
 */
class DatabaseTimeSeriesRepository

	extends EntityRepository
	implements ITimeSeriesRepository{

	/** findById
	 *
	 * 	Returns a TimeSeries matching the $id exactly.
	 *
	 * @param string $id
	 * @return PersistedTimeSeries|NULL
	 */
	public function findById($id){
		return $this->findOneBy(["id" => $id]);
	}

	/** findAll
	 *
	 * 	Returns all the time series.
	 *
	 * @return array
	 */
	public function findAll(){
		return parent::findAll();
	}

	public function findManyByIds(...$ids){
		$query = $this->_em->createQueryBuilder()
			->select("timeSeries")
			->from(PersistedTimeSeries::class, "timeSeries");

		return $query->where($query->expr()->in("timeSeries.id", $ids))
			->getQuery()
			->getResult();
	}

}