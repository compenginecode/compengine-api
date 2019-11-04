<?php

namespace DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\Neighbourhood;

use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;

/**
 * Class Neighbourhood
 * @package DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\Neighbourhood
 */
class Neighbourhood {

	/** $rootTimeSeries
	 *
	 * 	The root time series. Note that comparisons may be done
	 * 	on time series that are yet persisted, in which case this
	 * 	might be NULL.
	 *
	 * @var NULL|PersistedTimeSeries
	 */
	private $rootPersistedTimeSeries = NULL;

	/** $edges
	 *
	 * 	An array of edges, consisting of an array ["from", "to"].
	 *
	 * @var array
	 */
	private $edges = [];

	/** $nodes
	 *
	 * 	An array of nodes and their basic information.
	 *
	 * @var array
	 */
	private $nodes = [];

	/** getRootId
	 *
	 * 	Returns a string to use as an ID for the root note. As per the $rootPersistedTimeSeries
	 * 	description, if the neighbourhood is centred on a time series yet persisted, we return
	 * 	a pseudo ID.
	 *
	 * @return string
	 */
	protected function getRootId(){
		$root = $this->rootPersistedTimeSeries;
		if (NULL === $root){
			return "root";
		}

		/** @var $root PersistedTimeSeries */
		return $root->getId();
	}

	public function nodeExists($nodeId){
		$result = FALSE;
		foreach($this->nodes as $aNode){
			if ($aNode["id"] === $nodeId){
				$result = TRUE;
				break;
			}
		}

		return $result;
	}

	/** __construct
	 *
	 * 	Neighbourhood constructor.
	 *
	 * @param NULL|PersistedTimeSeries $rootTimeSeries
	 */
	public function __construct($rootTimeSeries = NULL){
		$this->rootPersistedTimeSeries = $rootTimeSeries;

		$this->nodes[] = array(
			"id" => $this->getRootId(),
			"similarity" => 0,
			"type" => "first-tier",
			"isRoot" => TRUE
		);
	}

	/** addNodeToRoot
	 *
	 * 	Adds a node to the root.
	 *
	 * @param $timeSeriesId
	 * @param $similarityScore
	 */
	public function addNodeToRoot($timeSeriesId, $similarityScore){
	    // don't add the root node to itself
	    if ($timeSeriesId === $this->getRootId()) {
	        return;
        }

		$this->edges[] = array(
			"from" => $this->getRootId(),
			"to" => $timeSeriesId,
			"length" => $similarityScore
		);

		if (!$this->nodeExists($timeSeriesId)) {
			$this->nodes[] = array(
				"id" => $timeSeriesId,
				"similarity" => $similarityScore,
				"type" => "first-tier",
				"isRoot" => FALSE
			);
		}
	}

	/** addNodeToNode
	 *
	 * 	Adds a node to another parent node.
	 *
	 * @param $parentNodeId
	 * @param $timeSeriesId
	 * @param $similarityScore
	 */
	public function addNodeToNode($parentNodeId, $timeSeriesId, $similarityScore){
		$this->edges[] = array(
			"from" => $parentNodeId,
			"to" => $timeSeriesId,
			"length" => $similarityScore
		);

		$type = "second-tier";
		if (in_array("root", [strtolower($parentNodeId), strtolower($timeSeriesId)])){
			$type = "first-tier";
		}

		if (!$this->nodeExists($timeSeriesId)) {
			$this->nodes[] = array(
				"id" => $timeSeriesId,
				"similarity" => $similarityScore,
				"type" => $type,
				"isRoot" => FALSE
			);
		}
	}

	/** getEdges
	 *
	 * 	Returns the edges.
	 *
	 * @return array
	 */
	public function getEdges() {
		return $this->edges;
	}

	/** getNodes
	 *
	 * 	Returns the nodes.
	 *
	 * @return array
	 */
	public function getNodes() {
		return $this->nodes;
	}

}