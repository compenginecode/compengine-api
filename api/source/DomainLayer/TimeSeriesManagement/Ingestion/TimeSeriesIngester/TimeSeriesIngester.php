<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester;

use DI\Container;
use DomainLayer\ORM\TimeSeries\ComparableTimeSeries\ComparableTimeSeries;
use DomainLayer\ORM\TimeSeries\IngestedTimeSeries\IngestedTimeSeries;
use DomainLayer\ORM\TopLevelCategory\TopLevelCategory;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonService\Exceptions\EEmptyTimeSeries;
use DomainLayer\TimeSeriesManagement\Ingestion\Converters\IConverter;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorGenerationService\FeatureVectorGenerationService;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorHashService\FeatureVectorHashService;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorNormalizationService\FeatureVectorNormalizationService;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\MappingManifestGenerationService\MappingManifestGenerationService;
use DomainLayer\TimeSeriesManagement\Ingestion\Preprocessors\IPreprocessor;
use DomainLayer\TimeSeriesManagement\Ingestion\Preprocessors\Truncator\Truncator;
use DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester\Exceptions\ETruncationWarning;
use DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester\Exceptions\EUnsupportedFileExtension;
use Sabre\Event\EventEmitterTrait;

/**
 * Class TimeSeriesIngester
 * @package DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester
 */
class TimeSeriesIngester {

	use EventEmitterTrait;

	/** Events that are emitted from this class */
	const EVENT_PROCESS_STARTED = "process:started";
	const EVENT_PROCESS_FINISHED = "process:finished";
	const EVENT_CONVERSION_STARTED = "conversion:started";
	const EVENT_CONVERSION_FINISHED = "conversion:finished";
	const EVENT_PRE_PROCESSING_STARTED = "preprocess:started";
	const EVENT_PRE_PROCESSING_FINISHED = "preprocess:finished";

	/** $container
	 *
	 * 	Dependency injection container. Used so we can dynamically lookup what
	 * 	converter class to use.
	 *
	 * @var Container
	 */
	private $container;

	private $featureVectorGenerationService;

	private $featureVectorNormalizationService;

	private $featureVectorHashService;

	/** $mappingManifestGenerationService
	 *
	 * 	This service exposes factory methods to create a mapping manifest
	 *  used for feature vector generation.
	 *
	 * @var MappingManifestGenerationService
	 */
	private $mappingManifestGenerationService;

	/** $allocatedConversions
	 *
	 * 	An array of mappings. The key represents the file extension, and the right is the appropriate
	 * 	fully qualified class that is able to convert files of that extension into a time series array.
	 * 	Note that each class must implement the IConverter interface.
	 *
	 * @var array of IConverter
	 */
	private $allocatedConversions = array(
		"txt" => "DomainLayer\\TimeSeriesManagement\\Ingestion\\Converters\\CSVConverter\\CSVConverter",
		"dat" => "DomainLayer\\TimeSeriesManagement\\Ingestion\\Converters\\CSVConverter\\CSVConverter",
		"csv" => "DomainLayer\\TimeSeriesManagement\\Ingestion\\Converters\\CSVConverter\\CSVConverter",
		"mp3" => "DomainLayer\\TimeSeriesManagement\\Ingestion\\Converters\\MP3Converter\\MP3Converter",
		"wav" => "DomainLayer\\TimeSeriesManagement\\Ingestion\\Converters\\WavConverter\\WavConverter",
		"xlsx" => "DomainLayer\\TimeSeriesManagement\\Ingestion\\Converters\\ExcelConverter\\ExcelConverter",
		"xls" => "DomainLayer\\TimeSeriesManagement\\Ingestion\\Converters\\ExcelConverter\\ExcelConverter",
	);

	/** $preprocessor
	 *
	 * 	The preprocess instance used to alter time series.
	 *
	 * @var IPreprocessor
	 */
	private $preprocessor;

	/** getConverter
	 *
	 * 	Returns an instance of the appropriate IConverter class that is able to
	 * 	convert the given file to a time series representation. If no converter
	 * 	exists, EUnsupportedFileExtension is thrown.
	 *
	 * @param $filePath
	 * @return IConverter|null
	 * @throws EUnsupportedFileExtension
	 * @throws \DI\NotFoundException
	 */
	protected function getConverter($filePath){
		$extension = pathinfo($filePath, PATHINFO_EXTENSION);
		$converter = NULL;
		foreach($this->allocatedConversions as $anExtension => $fullyQualifiedClassName){
			if ($extension === $anExtension){
				$converter = $this->container->get($fullyQualifiedClassName);
				break;
			}
		}

		if (NULL === $converter){
			throw new EUnsupportedFileExtension($extension);
		}

		return $converter;
	}

	/**
	 * TimeSeriesIngester constructor.
	 * @param Container $container
	 * @param Truncator $truncator
	 * @param FeatureVectorGenerationService $featureVectorGenerationService
	 * @param FeatureVectorNormalizationService $featureVectorNormalizationService
	 * @param FeatureVectorHashService $featureVectorHashService
	 * @param MappingManifestGenerationService $mappingManifestGenerationService
	 */
	public function __construct(Container $container, Truncator $truncator,
		FeatureVectorGenerationService $featureVectorGenerationService,
		FeatureVectorNormalizationService $featureVectorNormalizationService,
		FeatureVectorHashService $featureVectorHashService,
		MappingManifestGenerationService $mappingManifestGenerationService){

		$this->container = $container;
		$this->preprocessor = $truncator;
		$this->featureVectorGenerationService = $featureVectorGenerationService;
		$this->featureVectorNormalizationService = $featureVectorNormalizationService;
		$this->featureVectorHashService = $featureVectorHashService;
		$this->mappingManifestGenerationService = $mappingManifestGenerationService;
	}

	/** ingestFile
	 *
	 * 	Ingests a given file and returns a time series representation of it.
	 *
	 * @param $filePath
     * @param $ignoreTruncationWarning
	 * @return IngestedTimeSeries
	 * @throws EUnsupportedFileExtension
     * @throws EEmptyTimeSeries
	 */
	public function ingestFile($filePath, $ignoreTruncationWarning = false){
	    $dataPoints = $this->getDataPoints($filePath, $ignoreTruncationWarning);

        if (count($dataPoints) < 100){
            throw new EEmptyTimeSeries();
        }

        $manifest = $this->mappingManifestGenerationService->generateMappingManifestForCurrentFVFamily();
		$rawFeatureVector = $this->featureVectorGenerationService->generateFeatureVector($dataPoints, $manifest);
		$normalizedFeatureVector = $this->featureVectorNormalizationService->normalizeFeatureVector($rawFeatureVector, $manifest);

		$fingerprint = $this->featureVectorHashService->generateFingerprint(
			$normalizedFeatureVector,
			TopLevelCategory::unknown()
		);

		return new IngestedTimeSeries($dataPoints, $rawFeatureVector, $normalizedFeatureVector, $fingerprint);

	}

    public function getDataPoints($filePath, $ignoreTruncationWarning = false) {
        $this->emit(self::EVENT_PROCESS_STARTED);
        $converter = $this->getConverter($filePath);

        /** Convert the file to a time series */
        $this->emit(self::EVENT_CONVERSION_STARTED, ["type" => $converter->getConversionType()]);
        $dataPoints = $converter->convertToTimeSeries($filePath);
        $this->emit(self::EVENT_CONVERSION_FINISHED);

        if ($this->preprocessor->requiresPreprocessing($dataPoints)){
            if (! $ignoreTruncationWarning) {
                throw new ETruncationWarning();
            }

            $this->emit(self::EVENT_PRE_PROCESSING_STARTED);
            $dataPoints = $this->preprocessor->preProcessTimeSeries($dataPoints);
            $this->emit(self::EVENT_PRE_PROCESSING_FINISHED);
        }

        $this->emit(self::EVENT_PROCESS_FINISHED);

        return $dataPoints;
	}

}