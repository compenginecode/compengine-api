<?php

namespace PresentationLayer\Routes\PublicInterface\Timeseries\Get;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Metric\Metric;
use \PalePurple\RateLimit\Adapter\Predis as PredisAdapter;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\PublicAPI\DownloadService\DownloadService;
use DomainLayer\TimeSeriesManagement\TimeSeriesRenderer\TimeSeriesRenderer;
use PalePurple\RateLimit\RateLimit;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routing\StatusCode\TooManyRequests;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

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
	 * @var TimeSeriesRenderer
	 */
	private $timeSeriesRenderer;

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
	 * Get constructor.
	 * @param EntityManager $entityManager
	 * @param TimeSeriesRenderer $timeSeriesRenderer
	 * @param DownloadService $downloadService
	 */
	public function __construct(EntityManager $entityManager, TimeSeriesRenderer $timeSeriesRenderer,
		DownloadService $downloadService)
	{
		$this->entityManager = $entityManager;
		$this->timeSeriesRenderer = $timeSeriesRenderer;
	}

	/**
	 * @throws EInvalidInputs
	 */
	public function execute()
	{
		$adapter = new PredisAdapter($this->getPredisClient());
		$rateLimit = new RateLimit("public", 60, 60, $adapter);

		if ($rateLimit->check($_SERVER['REMOTE_ADDR'])) {
			if (!isset($_GET["name"])) {
				throw new EInvalidInputs("The 'name' query parameter is required.");
			}

			/** @var PersistedTimeSeries $timeSeries */
			$timeSeries = $this->entityManager->getRepository(PersistedTimeSeries::class)
				->findOneBy(["name" => $_GET["name"]]);

			if (null === $timeSeries) {
				throw new EInvalidInputs("No timeseries found with the name given.");
			}

			$this->response->setReturnBody(new JSONBody($this->timeSeriesRenderer->renderTimeSeriesForPublic($timeSeries)));
			$this->recordMetric('API request - timeseries search.');
		} else {
			$this->response->setReturnBody(new JSONBody(['message' => 'Rate limit exceeded.']));
			$this->response->setStatusCode(new TooManyRequests());
			$this->recordMetric('API rate limit reached.');
		}

	}

}