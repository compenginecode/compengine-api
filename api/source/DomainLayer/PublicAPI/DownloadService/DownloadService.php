<?php

namespace DomainLayer\PublicAPI\DownloadService;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\TimeSeriesManagement\TimeSeriesRenderer\TimeSeriesRenderer;

/**
 * Class DownloadService
 * @package DomainLayer\PublicAPI\DownloadService
 */
class DownloadService {

	/**
	 * @var TimeSeriesRenderer
	 */
	private $timeSeriesRenderer;

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	protected function getPageCount(Category $category)
	{
		$ids = array_map(function (Category $category) {
			return $category->getId();
		}, $this->getAllCategories($category));

		return (int)$this->entityManager->createQueryBuilder()
			->select('count(timeseries.id)')
			->from(PersistedTimeSeries::class, 'timeseries')
			->where('timeseries.category IN (:pCategories)')
			->setParameter('pCategories', $ids)
			->getQuery()
			->getResult()[0][1];
	}

	/**
	 * @param Category $category
	 * @return array
	 */
	protected function getAllCategories(Category $category)
	{
		$recurse = function (Category $category, &$buffer) use (&$recurse) {
			foreach ($category->getChildren() as $aChildCategory) {
				$recurse($aChildCategory, $buffer);
			}

			$buffer[] = $category;
		};

		$buffer = [];
		$recurse($category, $buffer);
		return $buffer;
	}

	/**
	 * DownloadService constructor.
	 * @param TimeSeriesRenderer $timeSeriesRenderer
	 */
	public function __construct(TimeSeriesRenderer $timeSeriesRenderer, EntityManager $entityManager)
	{
		$this->timeSeriesRenderer = $timeSeriesRenderer;
		$this->entityManager = $entityManager;
	}

	/**
	 * @param Category $category
	 * @return array
	 */
	public function downloadAsJSON(Category $category, $page)
	{
		$totalPages = $this->getPageCount($category);
		$pageSize = 10;

		$ids = array_map(function (Category $category) {
			return $category->getId();
		}, $this->getAllCategories($category));

		$timeSeries = $this->entityManager->createQueryBuilder()
			->select('timeseries')
			->from(PersistedTimeSeries::class, 'timeseries')
			->where('timeseries.category IN (:pCategories)')
			->setParameter('pCategories', $ids)
			->getQuery()
			->setMaxResults($pageSize)
			->setFirstResult($page * $pageSize)
			->getResult();

		$buffer = [];
		foreach ($timeSeries as $aTimeSeries) {
			$buffer[] = $this->timeSeriesRenderer->renderTimeSeriesForPublic($aTimeSeries);
		}

		return array(
			'pageSize' => $pageSize,
			'totalPages' => ceil($totalPages / $pageSize) - 1,
			'currentPage' => $page,
			'timeSeries' => $buffer
		);
	}

}