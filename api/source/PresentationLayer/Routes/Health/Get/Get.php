<?php

namespace PresentationLayer\Routes\Health\Get;

use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Google\OAuth\Start\Get
 */
class Get extends AbstractRoute{

    /** execute
     *
     *  Route execution.
     *
     */
    public function execute(){
        set_time_limit(0);

        $this->response->setReturnBody(new JSONBody(array(
            "health" => "healthy"
        )));
    }

}