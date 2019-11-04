<?php

namespace PresentationLayer\Routes\PublicInterface\Timeseries\Filter\Get;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\Metric\Metric;
use DomainLayer\PublicAPI\DownloadService\DownloadService;
use PalePurple\RateLimit\RateLimit;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routing\StatusCode\TooManyRequests;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;
use \PalePurple\RateLimit\Adapter\Predis as PredisAdapter;

/**
 * Class Get
 * @package PresentationLayer\Routes\PublicInterface\Timeseries\Get
 */
class Get extends AbstractRoute {

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var DownloadService
	 */
	private $downloadService;

	/**
	 * @return \Predis\Client
	 */
	protected function getPredisClient()
	{
		global $redis;
		return $redis;
	}

	protected function recordMetric($name){
		$metric = new Metric($name);
		$this->entityManager->persist($metric);
		$this->entityManager->flush();
	}

	/**
	 * @param Category $category
	 * @return array
	 */
	protected function returnBulkJSONPayload(Category $category, $page)
	{
		return $this->downloadService->downloadAsJSON($category, $page);
	}

	/**
	 * Get constructor.
	 * @param EntityManager $entityManager
	 * @param DownloadService $downloadService
	 */
	public function __construct(EntityManager $entityManager, DownloadService $downloadService)
	{
		$this->entityManager = $entityManager;
		$this->downloadService = $downloadService;
	}

	/**
	 * @throws EInvalidInputs
	 */
	public function execute()
	{
		set_time_limit(300);

		$adapter = new PredisAdapter($this->getPredisClient());
		$rateLimit = new RateLimit("public", 60, 60, $adapter);

		if ($rateLimit->check($_SERVER['REMOTE_ADDR'])) {
			foreach (["format", "category"] as $aKey) {
				if (!isset($_GET[$aKey])) {
					throw new EInvalidInputs("The '$aKey' query parameter is required.");
				}
			}

			/** @var Category $category */
			$category = $this->entityManager->getRepository(Category::class)
				->findOneBy(['name' => $_GET['category']]);

			if (null === $category) {
				throw new EInvalidInputs("No category found with the name given.");
			}

			$page = empty($_GET['page']) ? 0 : (int)$_GET['page'];
			$this->response->setReturnBody(new JSONBody($this->returnBulkJSONPayload($category, $page)));

			$this->recordMetric('API request - category search.');
		} else {
			$this->response->setReturnBody(new JSONBody(['message' => 'Rate limit exceeded.']));
			$this->response->setStatusCode(new TooManyRequests());
			$this->recordMetric('API rate limit reached.');
		}
	}

}