<?php

namespace DomainLayer\TimeSeriesManagement\Comparison\UploadService;

use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\TimeSeriesManagement\Comparison\UploadService\Exceptions\EComparisonKeyMissing;
use DomainLayer\TimeSeriesManagement\Comparison\UploadService\Exceptions\EFileMissing;
use InfrastructureLayer\Caching\CacheAdaptor\ICacheAdaptor;
use InfrastructureLayer\Crypto\TokenGenerator\ITokenGenerator;

/**
 * Class UploadService
 * @package DomainLayer\TimeSeriesManagement\Comparison\UploadService
 */
class UploadService {

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

	/** formKey
	 *
	 * 	Converts a local key to a globally unique, collision free key.
	 *
	 * @param $comparisonKey
	 * @return string
	 */
	protected function formKey($comparisonKey){
		return "upload-service-" . $comparisonKey;
	}

	/** getCacheKeyExpireTime
	 *
	 * 	Returns the expiry time for all objects in the cache.
	 *
	 * @return int
	 */
	protected function getCacheKeyExpireTime(){
		return $this->siteAttributeRepository->getComparisonResultCacheTime();
	}

	/** wrap
	 *
	 * 	Returns a consistent mechanism for wrapping arbitrary parameters into an array.
	 *
	 * @param $temporaryFilePath
	 * @param $originalFileName
	 * @return array
	 */
	protected function wrap($temporaryFilePath, $originalFileName){
		return array(
			"temporaryFilePath" => $temporaryFilePath,
			"originalFileName" => $originalFileName
		);
	}

	/** __construct
	 *
	 * 	UploadService constructor.
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

	/** getFileData
	 *
	 * 	Returns the file data from a given comparison key.
	 *
	 * @param $comparisonKey
	 * @return mixed
	 * @throws EComparisonKeyMissing
	 */
	public function getFileData($comparisonKey){
		$result = $this->cacheAdaptor->getValue($this->formKey($comparisonKey));
		if (NULL !== $result){
			return json_decode($result, JSON_OBJECT_AS_ARRAY);
		}

		throw new EComparisonKeyMissing($comparisonKey);
	}

	/** getComparisonKey
	 *
	 * 	Get a comparison key from a file in $_FILE. The key for $_FILE is called the formKey.
	 *
	 * @param $formKey
	 * @return string
	 * @throws EFileMissing
	 */
	public function getComparisonKey($formKey){
		if (isset($_FILES[$formKey])){

			$tempName = $_FILES[$formKey]["tmp_name"];
			$realName = $_FILES[$formKey]["name"];
			$newName = pathinfo($tempName, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($tempName, PATHINFO_FILENAME) .
				"." . pathinfo($realName, PATHINFO_EXTENSION);

			rename($tempName, $newName);
			$payload = $this->wrap($newName, $realName);
			$key = $this->tokenGenerator->generateToken(25);

			$this->cacheAdaptor->setValue($this->formKey($key), json_encode($payload), $this->getCacheKeyExpireTime());
			return $key;
		}

		throw new EFileMissing($formKey);
	}

}