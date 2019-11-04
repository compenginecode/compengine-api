<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester\Exceptions;

class ETruncationWarning extends \Exception
{
    public function __construct(){
        parent::__construct("The time series will be truncated.");
    }
}
