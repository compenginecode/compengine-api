<?php

namespace DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository;

use Doctrine\Common\Collections\Collection;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;

/**
 * Interface ITimeSeriesRepository
 * @package DomainLayer\ORM\Timeseries\Repository
 */
interface ITimeSeriesRepository {

	/** findById
	 *
	 * 	Returns a TimeSeries matching the $id exactly.
	 *
	 * @param string $id
	 * @return PersistedTimeSeries|NULL
	 */
	public function findById($id);

	/** findAll
	 *
	 * 	Returns all the time series.
	 *
	 * @return array
	 */
	public function findAll();

	public function findManyByIds(...$ids);

}