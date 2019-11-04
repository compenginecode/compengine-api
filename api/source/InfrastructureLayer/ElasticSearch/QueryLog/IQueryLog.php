<?php

namespace InfrastructureLayer\ElasticSearch\QueryLog;

/**
 * Interface IQueryLog
 * @package InfrastructureLayer\ElasticSearch\QueryLog
 */
interface IQueryLog {

	public function log(array $query);

}