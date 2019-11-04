<?php

namespace InfrastructureLayer\Queueing\Message;

/**
 * Class Message
 * @package InfrastructureLayer\Queueing\Message
 */
class Message {

	/**
	 * @var int
	 */
	private $submissionTime;

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var
	 */
	protected $fullyQualifiedTaskName;

	/**
	 * @var array
	 */
	protected $arguments;

	/** __construct
	 *
	 * 	Message constructor.
	 *
	 * @param $fullyQualifiedTaskName
	 * @param array $arguments
	 */
	public function __construct($fullyQualifiedTaskName, array $arguments){
		$this->fullyQualifiedTaskName = $fullyQualifiedTaskName;
		$this->arguments = $arguments;
		$this->submissionTime = time();
		$this->id = md5($this->submissionTime);
	}

	public function serialize(){
		return serialize($this);
	}

	public function getFullyQualifiedTaskName(){
		return $this->fullyQualifiedTaskName;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

}