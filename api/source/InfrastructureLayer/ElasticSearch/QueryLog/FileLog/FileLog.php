<?php

namespace InfrastructureLayer\ElasticSearch\QueryLog\FileLog;

use InfrastructureLayer\ElasticSearch\QueryLog\IQueryLog;

/**
 * Class FileLog
 * @package InfrastructureLayer\ElasticSearch\QueryLog\FileLog
 */
class FileLog implements IQueryLog{

	public function log(array $query) {
		$logEntry = json_encode($query, JSON_PRETTY_PRINT);
		file_put_contents(ROOT_PATH . "/private/logs/elastic-search-query.log", $logEntry, FILE_APPEND);
	}

}