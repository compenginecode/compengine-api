<?php

use \ConfigurationLayer\ApplicationConfigFactory\ApplicationConfigFactory;
use \ConfigurationLayer\ConfigurationAnnotations\DefaultAnnotations\DefaultConfigurationAnnotations;

define("ROOT_PATH", realpath( __DIR__ . "/.."));
require_once ROOT_PATH . "/vendor/autoload.php";

/** We create an application level cache for use by DI*/
$cache = new \Doctrine\Common\Cache\FilesystemCache(ROOT_PATH . "/private/temp");

$configurationAnnotations = new DefaultConfigurationAnnotations();
$configurationAnnotations->setRootDirectory(ROOT_PATH);

$configuration = ApplicationConfigFactory::createFromEnvironment(ROOT_PATH . "/private/configuration/configuration.ini");
$configuration->setAnnotations($configurationAnnotations);

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$isDevMode = true;
$config = Setup::createXMLMetadataConfiguration([ROOT_PATH . "/source/ConfigurationLayer/DoctrineMappings"], $isDevMode);
$config->setProxyDir(ROOT_PATH . "/private/proxies");
$config->addCustomNumericFunction('INT', \InfrastructureLayer\Query\CastAsInteger\CastAsInteger::class);
$config->addCustomStringFunction('REPLACE', DoctrineExtensions\Query\Mysql\Replace::class);
$config->addCustomStringFunction('REGEXP', DoctrineExtensions\Query\Mysql\Regexp::class);

$conn = array(
	"dbname" => $configuration->get("db_name"),
	"user" => $configuration->get("db_user"),
	"password" => $configuration->get("db_password"),
	"host" => $configuration->get("db_host"),
	"driver" => "pdo_mysql",
);

/** Build elasticsearch */
$clientBuilder = \Elasticsearch\ClientBuilder::create();
$clientBuilder->setHosts([$configuration->get("elasticsearch_host")]);
$elasticSearch = $clientBuilder->build();

$eventManager = new \Doctrine\Common\EventManager();
$entityManager = EntityManager::create($conn, $config, $eventManager);

/** Here we create the global DI container */
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(ROOT_PATH . "/source/ConfigurationLayer/DependencyInjectionOverrides/dependencies.php");

// $builder->setDefinitionCache($cache);

$builder->useAutowiring(true);
$builder->useAnnotations(true);
$container = $builder->build();

$eventManager->addEventSubscriber($container->get("DomainLayer\\ORM\\TimeSeries\\PersistedTimeSeries\\Events\\PersistedTimeSeriesEventSubscriber"));

/** Build the cache */
$redis = new \Predis\Client(array(
	"scheme" => $configuration->get("redis_master_scheme"),
	"host" => $configuration->get("redis_master_host"),
	"port" => $configuration->get("redis_master_port"),
	"database" => $configuration->get("redis_master_database"),
	"timeout" => $configuration->get("redis_master_timeout"),
));