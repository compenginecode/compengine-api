<?php

global $configuration;
global $entityManager;
global $redis;
global $elasticSearch;

return [

    "PrimaryConfiguration" => $configuration,
	"ConfigurationLayer\\ApplicationConfig\\ApplicationConfig" => $configuration,
	"Doctrine\\ORM\\EntityManager" => $entityManager,
	"Predis\\Client" => $redis,
	"Elasticsearch\\Client" => $elasticSearch,

	/** Set the default instance for the ICaching interface as Redis */
	"PrimaryCache" => DI\object("InfrastructureLayer\\Caching\\CacheAdaptor\\RedisCacheAdaptor\\RedisCacheAdaptor")
		->constructor($configuration),

	"InfrastructureLayer\\Caching\\CacheAdaptor\\ICacheAdaptor" => DI\link("PrimaryCache"),

	"InfrastructureLayer\\ElasticSearch\\QueryLog\\IQueryLog" => DI\Link("InfrastructureLayer\\ElasticSearch\\QueryLog\\FileLog\\FileLog"),

	"InfrastructureLayer\\Crypto\\TokenGenerator\\ITokenGenerator" =>
		DI\link("InfrastructureLayer\\Crypto\\TokenGenerator\\CryptoTokenGenerator\\CryptoTokenGenerator"),

    "InfrastructureLayer\\EmailGateway\\IEmailGateway" =>
        DI\link("InfrastructureLayer\\EmailGateway\\SendGridEmailGateway\\SendGridEmailGateway"),

    \ReCaptcha\ReCaptcha::class =>
        DI\object()->constructorParameter("secret", $configuration->get("recaptcha_secret")),

    "SitemapPHP\\Sitemap" => DI\object()->constructorParameter("domain", "https://www.comp-engine.org"),

	/** Mapping repositories to Doctrine repository DI container */
	"DomainLayer\\ORM\\SiteAttribute\\Repository\\DatabaseSiteAttributeRepository" => $entityManager->getRepository("DomainLayer\\ORM\\SiteAttribute\\SiteAttribute"),
	"DomainLayer\\ORM\\Category\\Repository\\DatabaseCategoryRepository" => $entityManager->getRepository("DomainLayer\\ORM\\Category\\Category"),
	"DomainLayer\\ORM\\Tag\\Repository\\DatabaseTagRepository" => $entityManager->getRepository("DomainLayer\\ORM\\Tag\\Tag"),
	"DomainLayer\\ORM\\Source\\Repository\\DatabaseSourceRepository" => $entityManager->getRepository("DomainLayer\\ORM\\Source\\Source"),
	"DomainLayer\\ORM\\Contributor\\Repository\\DatabaseContributorRepository" => $entityManager->getRepository("DomainLayer\\ORM\\Contributor\\Contributor"),
	"DomainLayer\\ORM\\TimeSeries\\PersistedTimeSeries\\Repository\\DatabaseTimeSeriesRepository" => $entityManager->getRepository("DomainLayer\\ORM\\TimeSeries\\PersistedTimeSeries\\PersistedTimeSeries"),
	"DomainLayer\\ORM\\FeatureVectorFamily\\Repository\\DatabaseFeatureVectorFamilyRepository" => $entityManager->getRepository("DomainLayer\\ORM\\FeatureVectorFamily\\FeatureVectorFamily"),
	"DomainLayer\\ORM\\Notification\\Repository\\DatabaseNotificationRepository" => $entityManager->getRepository("DomainLayer\\ORM\\Notification\\Notification"),

	/** Mapping repository interfaces to database backed concretions */
	"DomainLayer\\ORM\\SiteAttribute\\Repository\\ISiteAttributeRepository" => DI\link("DomainLayer\\ORM\\SiteAttribute\\Repository\\FullSiteAttributeRepository"),
	"DomainLayer\\ORM\\Category\\Repository\\ICategoryRepository" => DI\link("DomainLayer\\ORM\\Category\\Repository\\DatabaseCategoryRepository"),
	"DomainLayer\\ORM\\Tag\\Repository\\ITagRepository" => DI\link("DomainLayer\\ORM\\Tag\\Repository\\DatabaseTagRepository"),
	"DomainLayer\\ORM\\Source\\Repository\\ISourceRepository" => DI\link("DomainLayer\\ORM\\Source\\Repository\\DatabaseSourceRepository"),
	"DomainLayer\\ORM\\Contributor\\Repository\\IContributorRepository" => DI\link("DomainLayer\\ORM\\Contributor\\Repository\\DatabaseContributorRepository"),
	"DomainLayer\\ORM\\TimeSeries\\PersistedTimeSeries\\Repository\\ITimeSeriesRepository" => DI\link("DomainLayer\\ORM\\TimeSeries\\PersistedTimeSeries\\Repository\\DatabaseTimeSeriesRepository"),
	"DomainLayer\\ORM\\FeatureVectorFamily\\Repository\\IFeatureVectorFamilyRepository" => DI\link("DomainLayer\\ORM\\FeatureVectorFamily\\Repository\\DatabaseFeatureVectorFamilyRepository"),
	"DomainLayer\\ORM\\Notification\\Repository\\INotificationRepository" => DI\link("DomainLayer\\ORM\\Notification\\Repository\\DatabaseNotificationRepository"),

	/** Link up the INormalizer interface to the ElasticSearch normalizer */
	"DomainLayer\\TimeSeriesManagement\\Ingestion\\FeatureVectorGeneration\\FeatureVectorNormalizationService\\Normalizers\\INormalizer" =>
		DI\link("DomainLayer\\TimeSeriesManagement\\Ingestion\\FeatureVectorGeneration\\FeatureVectorNormalizationService\\Normalizers\\ElasticSearchNormalizer\\ElasticSearchNormalizer")

];