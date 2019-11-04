<?php

namespace DomainLayer\TimeSeriesManagement\Comparison\ComparisonStore;

use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use InfrastructureLayer\Caching\CacheAdaptor\ICacheAdaptor;
use InfrastructureLayer\Crypto\TokenGenerator\ITokenGenerator;

/**
 * Class ComparisonStore
 * @package DomainLayer\TimeSeriesManagement\Comparison\ComparisonStore
 */
class ComparisonStore {

	/** $cacheAdaptor
	 *
	 * 	Interface to a cache.
	 *
	 * @var ICacheAdaptor
	 */
	protected $cacheAdaptor;

	/** $tokenGenerator
	 *
	 * 	Interface to a token generator.
	 *
	 * @var ITokenGenerator
	 */
	protected $tokenGenerator;

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
	 * @param $resultKey
	 * @return string
	 */
	protected function formKey($resultKey){
		return "comparison-store-" . $resultKey;
	}

	/** __construct
	 *
	 * 	ComparisonStore constructor.
	 *
	 * @param ICacheAdaptor $cacheAdaptor
	 * @param ITokenGenerator $tokenGenerator
	 * @param ISiteAttributeRepository $siteAttributeRepository
	 */
	public function __construct(ICacheAdaptor $cacheAdaptor, ITokenGenerator $tokenGenerator,
		ISiteAttributeRepository $siteAttributeRepository){

		$this->cacheAdaptor = $cacheAdaptor;
		$this->tokenGenerator = $tokenGenerator;
		$this->siteAttributeRepository = $siteAttributeRepository;
	}

	/** temporarilyStoreComparisonResult
	 *
	 * 	Temporarily store the comparison result. A token handle is returned.
	 *
	 * @param array $result
	 * @return string
	 */
	public function temporarilyStoreComparisonResult(array $result){
		$resultKey = $this->tokenGenerator->generateToken(16);
		$this->cacheAdaptor->setValue($this->formKey($resultKey), serialize($result), $this->getCacheKeyExpireTime());

		return $resultKey;
	}

	/** retrieveComparisonResult
	 *
	 * 	Returns the comparison result by its token handle.
	 *
	 * @param $resultKey
	 * @return array
	 */
	public function retrieveComparisonResult($resultKey){
		return unserialize($this->cacheAdaptor->getValue($this->formKey($resultKey)));
	}

}