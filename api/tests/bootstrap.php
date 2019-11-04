<?php

use \ConfigurationLayer\ApplicationConfigFactory\ApplicationConfigFactory;
use \ConfigurationLayer\ConfigurationAnnotations\DefaultAnnotations\DefaultConfigurationAnnotations;

define("ROOT_PATH", realpath( __DIR__ . "/.."));
require_once ROOT_PATH . "/vendor/autoload.php";

/** We create an application level cache for use by DI*/
$cache = new \Doctrine\Common\Cache\FilesystemCache(ROOT_PATH . "/private/temp");

$configurationAnnotations = new DefaultConfigurationAnnotations();
$configurationAnnotations->setRootDirectory(ROOT_PATH);

$configuration = ApplicationConfigFactory::createFromFile(ROOT_PATH . "/private/configuration/configuration.ini", "testing");
$configuration->setAnnotations($configurationAnnotations);

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$isDevMode = true;
$config = Setup::createXMLMetadataConfiguration([ROOT_PATH . "/source/ConfigurationLayer/DoctrineMappings"], $isDevMode);
$config->setProxyDir(ROOT_PATH . "/private/proxies");

$conn = array(
	"dbname" => "comp-engine",
	"user" => "root",
	"password" => "",
	"host" => "localhost",
	"driver" => "pdo_mysql",
);

$entityManager = EntityManager::create($conn, $config);

/** Here we create the global DI container */
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(ROOT_PATH . "/source/ConfigurationLayer/DependencyInjectionOverrides/dependencies.php");

$builder->setDefinitionCache($cache);

$builder->useAutowiring(true);
$builder->useAnnotations(true);
$container = $builder->build();

/** Build elasticsearch */
$clientBuilder = \Elasticsearch\ClientBuilder::create();
$clientBuilder->setHosts(["http://localhost:9200"]);
$elasticSearch = $clientBuilder->build();