<?php

namespace PresentationLayer\ReturnBody;

use Yam\Route\Response\ReturnBody\IReturnBody;

/**
 * Class TextReturnBody
 * @package PresentationLayer\ReturnBody
 */
final class TextReturnBody implements IReturnBody{

	/** $payload
	 *
	 * 	The string payload.
	 *
	 * @var string
	 */
	private $payload = "";

	/** __construct
	 *
	 * 	JSONBody constructor.
	 *
	 * @param $payload
	 */
	public function __construct($payload){
		$this->payload = $payload;
	}

	/** getAsResponseBody
	 *
	 * 	Returns the response body.
	 *
	 * @return string
	 */
	public function getAsResponseBody(){
		return $this->payload;
	}

}