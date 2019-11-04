<?php

namespace DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\NeighbourhoodRenderer;

use Doctrine\Common\Collections\Collection;
use DomainLayer\ORM\DomainEntity\DomainEntity;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository\ITimeSeriesRepository;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\Neighbourhood\Neighbourhood;
use DomainLayer\TimeSeriesManagement\Downsampler\LTTBDownsampler;

/**
 * Class NeighbourhoodRenderer
 * @package DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\NeighbourhoodRenderer
 */
class NeighbourhoodRenderer {

	/** $timeSeriesRepository
	 *
	 * 	Interface to a repository for accessing time series.
	 *
	 * @var ITimeSeriesRepository
	 */
	private $timeSeriesRepository;

	/** $downsampler
	 *
	 * 	Interface to IDownsampler used to down sample the time series.
	 *
	 * @var LTTBDownsampler
	 */
	private $downsampler;

	/** __construct
	 *
	 * 	NeighbourhoodRenderer constructor.
	 *
	 * @param ITimeSeriesRepository $timeSeriesRepository
	 */
	public function __construct(ITimeSeriesRepository $timeSeriesRepository, LTTBDownsampler $LTTBDownsampler){
		$this->timeSeriesRepository = $timeSeriesRepository;
		$this->downsampler = $LTTBDownsampler;
	}

	/** renderNeighbourhood
	 *
	 * 	Renders a given Neighbourhood for use on the front end.
	 *
	 * @param Neighbourhood $neighbourhood
	 * @return array
	 */
	public function renderNeighbourhood(Neighbourhood $neighbourhood){
		$newNodesArray = [];

		/** We want to get an array of all the time series IDs */
		$ids = array_map(function($element){
			return $element["id"];
		}, $neighbourhood->getNodes());

//		/** We remove the root */
//		$ids = array_filter($ids, function($element){
//			return "root" !== $element;
//		});

		if (0 === count($ids)){
			return array(
				"edges" => [],
				"nodes" => []
			);
		}

		/** We then retrieve all the time series objects from Doctrine/MySQL in one call
		 * 	and define a lookup function to be used locally. */
		$timeSeriesArray = $this->timeSeriesRepository->findManyByIds(...$ids);
		$getTimeSeries = function($id) use ($timeSeriesArray){
			foreach($timeSeriesArray as $aTimeSeries){
				/** @var $aTimeSeries PersistedTimeSeries */
				if ($aTimeSeries->getId() === $id){
					return $aTimeSeries;
				}
			}

			return NULL;
		};

		foreach($neighbourhood->getNodes() as $aRawNode){
			$timeSeriesId = $aRawNode["id"];
			$similarityScore = $aRawNode["similarity"];
			$type = $aRawNode["type"];
			$isRoot = $aRawNode["isRoot"];

			$items = ["primary", "secondary", "tertiary", "quaternary"];
			/** Render common entries first. Remember, the time series may be based on a pseudo ID. */
			$common = array(
				"id" => $timeSeriesId,
				"similarityScore" => $similarityScore,
				"type" => $type,
				"colorScheme" => $items[array_rand($items)]
			);

			$timeSeries = $getTimeSeries($timeSeriesId);
			if (NULL !== $timeSeries){
				/** @var $timeSeries PersistedTimeSeries */
				$newNodesArray[] = array_merge($common, array(
					"isRoot" => $isRoot,
					"name" => $timeSeries->getName(),
					"dataPoints" => $timeSeries->getDownSampledDataPoints30(),
					"fullDataPoints" => $timeSeries->getDownSampledDataPoints1000(),
					"category" => $timeSeries->getCategory()->getName(),
					"tags" => $timeSeries->getTagNames(),
					"source" => (NULL === $timeSeries->getSource()) ? NULL : $timeSeries->getSource()->getName(),
					"topLevelCategory" => $timeSeries->getTopLevelCategory()->getName()
				));
			}
		}

		return array(
			"edges" => $neighbourhood->getEdges(),
			"nodes" => $newNodesArray
		);
	}

}