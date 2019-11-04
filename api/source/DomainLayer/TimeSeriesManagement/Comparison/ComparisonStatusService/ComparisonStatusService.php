<?php

namespace DomainLayer\TimeSeriesManagement\Comparison\ComparisonStatusService;

use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonStatus\ComparisonStatus;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonStatusService\Exceptions\EComparisonKeyNotFound;
use InfrastructureLayer\Caching\CacheAdaptor\ICacheAdaptor;

/**
 * Class ComparisonStatusService
 * @package DomainLayer\TimeSeriesManagement\Comparison
 */
class ComparisonStatusService {

	/** $cacheAdaptor
	 *
	 * 	Interface to a cache.
	 *
	 * @var ICacheAdaptor
	 */
	protected $cacheAdaptor;

	/** $siteAttributeRepository
	 *
	 * 	Repository to all site properties.
	 *
	 * @var ISiteAttributeRepository
	 */
	protected $siteAttributeRepository;

	/** getCacheKeyExpireTime
	 *
	 * 	Returns the expiry time for all objects in the cache.
	 *
	 * @return int
	 */
	protected function getCacheKeyExpireTime(){
		return $this->siteAttributeRepository->getComparisonResultCacheTime();
	}

	/** formKey
	 *
	 * 	Converts a local key to a globally unique, collision free key.
	 *
	 * @param $comparisonKey
	 * @return string
	 */
	protected function formKey($comparisonKey){
		return "comparison-status-service-" . $comparisonKey;
	}

	/** __construct
	 *
	 * 	ComparisonStatusService constructor.
	 *
	 * @param ICacheAdaptor $cacheAdaptor
	 * @param ISiteAttributeRepository $siteAttributeRepository
	 */
	public function __construct(ICacheAdaptor $cacheAdaptor, ISiteAttributeRepository $siteAttributeRepository){
		$this->cacheAdaptor = $cacheAdaptor;
		$this->siteAttributeRepository = $siteAttributeRepository;
	}

	/** updateStatus
	 *
	 * 	Update the status of a given comparison job in progress by its key.
	 *
	 * @param $comparisonKey
	 * @param ComparisonStatus $comparisonStatus
	 */
	public function updateStatus($comparisonKey, ComparisonStatus $comparisonStatus){
		$key = $this->formKey($comparisonKey);
		$this->cacheAdaptor->setValue($key, serialize($comparisonStatus), $this->getCacheKeyExpireTime());
	}

	/** getStatus
	 *
	 * 	Returns the status of the given job.
	 * @param $comparisonKey
	 * @return ComparisonStatus
	 * @throws EComparisonKeyNotFound
	 */
	public function getStatus($comparisonKey){
		$value = $this->cacheAdaptor->getValue($this->formKey($comparisonKey));
		if (NULL !== $value){
			return unserialize($value);
		}

		throw new EComparisonKeyNotFound($comparisonKey);
	}

}