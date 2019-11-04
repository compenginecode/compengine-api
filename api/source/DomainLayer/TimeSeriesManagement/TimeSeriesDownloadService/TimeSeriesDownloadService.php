<?php

namespace DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService;

use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository\ITimeSeriesRepository;
use InfrastructureLayer\Caching\CacheAdaptor\ICacheAdaptor;
use InfrastructureLayer\Crypto\TokenGenerator\ITokenGenerator;
use SendGrid\Content;
use SendGrid\Email;
use SendGrid\Mail;

/**
 * Class TimeSeriesDownloadService
 * @package DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService
 */
class TimeSeriesDownloadService {

    /** $cacheAdaptor
     *
     * 	Adaptor used to access a distributed cache.
     *
     * @var ICacheAdaptor
     */
    private $cacheAdaptor;

    /** $tokenGenerator
     *
     * 	Service used to generate random tokens.
     * @var ITokenGenerator
     */
    private $tokenGenerator;

    /** repository
     *
     *  The time series repository
     *
     * @var ITimeSeriesRepository
     */
    private $repository;

    const JSON_STORAGE_DIRECTORY = ROOT_PATH . "/private/temp/time-series/json/";
    const CSV_STORAGE_DIRECTORY = ROOT_PATH . "/private/temp/time-series/csv/";

    /** __construct
     *
     *  Constructor
     *
     * @param ICacheAdaptor $cacheAdaptor
     * @param ITokenGenerator $tokenGenerator
     * @param ITimeSeriesRepository $repository
     */
    public function __construct(ICacheAdaptor $cacheAdaptor, ITokenGenerator $tokenGenerator, ITimeSeriesRepository $repository) {
        $this->cacheAdaptor = $cacheAdaptor;
        $this->tokenGenerator = $tokenGenerator;
        $this->repository = $repository;
        if (!file_exists(TimeSeriesDownloadService::JSON_STORAGE_DIRECTORY)) {
            mkdir(TimeSeriesDownloadService::JSON_STORAGE_DIRECTORY, 0777, true);
        }
        if (!file_exists(TimeSeriesDownloadService::CSV_STORAGE_DIRECTORY)) {
            mkdir(TimeSeriesDownloadService::CSV_STORAGE_DIRECTORY, 0777, true);
        }
    }

    public function generateDownloadToken($email, $format) {
        $token = $this->tokenGenerator->generateToken(25);
        $data = [
            "emailAddress" => $email,
            "format" => $format
        ];
        $this->cacheAdaptor->setValue("time-series-" . $token, serialize($data), 2592000);

        return $token;
    }

    public function generateDownloadLinkEmail($email, $link) {
        global $configuration;

        $body = '
            <html>
                <body>
                    <p>You may download your time series at the following link:</p>
                    <p>[link]</p>
                    <p>Data is covered by the <a href="https://creativecommons.org/share-your-work/public-domain/cc0">CC0 license</a>.</p>
                </body>
            </html>
        ';
        $replacements = array(
            "[link]" => $link,
        );
        $body = str_replace(array_keys($replacements), array_values($replacements), $body);

        $mail = new Email();
        $mail
            ->addTo($email)
            ->setFrom($configuration->get("email_from"))
            ->setSubject("Your Time Series download link is ready")
            ->setHtml($body)
        ;

        return $mail;
    }

    public function zipFiles($fileName, array $files) {
        $archive = new \ZipArchive();
        $archivePath = TimeSeriesDownloadService::CSV_STORAGE_DIRECTORY . $fileName . ".zip";

        if ($archive->open($archivePath, \ZipArchive::CREATE) === FALSE) {
            throw new \Exception("Failed to create zip archive.");
        } else {
            foreach ($files as $file) {
                $archive->addFile($file, basename($file));
            }
            $archive->close();

            foreach ($files as $file) {
                unlink($file);
            }

            return $archivePath;
        }
    }

    public function generateTimeSeriesAsJson() {
        $timeseriesCollection = $this->repository->findAll();
        $output = [];
        /** @var PersistedTimeSeries $aTimeSeries */
        foreach ($timeseriesCollection as $aTimeSeries) {
            $output[] = [
                "timeseries_id" => $aTimeSeries->getId(),
                "timestamp_created" => $aTimeSeries->timestampCreated()->format("Y-m-d H:i:s"),
                "source" => $aTimeSeries->getSource() === NULL ? "" : $aTimeSeries->getSource()->getName(),
                "category" => $aTimeSeries->getCategory() === NULL ? "" : $aTimeSeries->getCategory()->getName(),
                "contributor" => $aTimeSeries->getContributor() === NULL ? "" : $aTimeSeries->getContributor()->getName(),
                "name" => $aTimeSeries->getName(),
                "description" => $aTimeSeries->getDescription(),
                "sampling_unit" => $aTimeSeries->getSamplingInformation()->getSamplingUnit(),
                "sampling_rate" => $aTimeSeries->getSamplingInformation()->getSamplingRate(),
                "datapoints" => $aTimeSeries->getDataPoints(),
            ];
        }

        $today = new \DateTime();
        $today = $today->format("Ymd");
        $fileName = "comp-engine-export." . $today . ".json";
        if (!file_put_contents(TimeSeriesDownloadService::JSON_STORAGE_DIRECTORY . $fileName, json_encode($output))) {
            throw new \Exception("Failed to create JSON file.");
        }

        return TimeSeriesDownloadService::JSON_STORAGE_DIRECTORY . $fileName;
    }

    public function generateTimeSeriesMetadataAsCsv() {
        $timeseriesCollection = $this->repository->findAll();
        $output = [];

        $output[] = [
            "timeseries_id", "timestamp_created", "source", "category", "contributor", "name", "description",
            "sampling_unit", "sampling_rate"
        ];

        /** @var PersistedTimeSeries $aTimeSeries */
        foreach ($timeseriesCollection as $aTimeSeries) {
            $output[] = [
                $aTimeSeries->getId(),
                $aTimeSeries->timestampCreated()->format("Y-m-d H:i:s"),
                $aTimeSeries->getSource() === NULL ? "" : $aTimeSeries->getSource()->getName(),
                $aTimeSeries->getCategory() === NULL ? "" : $aTimeSeries->getCategory()->getName(),
                $aTimeSeries->getContributor() === NULL ? "" : $aTimeSeries->getContributor()->getName(),
                $aTimeSeries->getName(),
                $aTimeSeries->getDescription(),
                $aTimeSeries->getSamplingInformation()->getSamplingUnit(),
                $aTimeSeries->getSamplingInformation()->getSamplingRate(),
            ];
        }

        $today = new \DateTime();
        $today = $today->format("Ymd");
        $fileName = "comp-engine-export-metadata." . $today . ".csv";

        $fp = fopen(TimeSeriesDownloadService::CSV_STORAGE_DIRECTORY . $fileName, 'w');
        if (!$fp) {
            throw new \Exception("Failed to create CSV file.");
        }
        foreach ($output as $item) {
            fputcsv($fp, $item);
        }
        fclose($fp);

        return TimeSeriesDownloadService::CSV_STORAGE_DIRECTORY . $fileName;
    }

    public function generateTimeSeriesDatapointsAsCsv() {
        $timeseriesCollection = $this->repository->findAll();
        $output = [];

        $output[] = [
            "timeseries_id", "datapoints",
        ];

        /** @var PersistedTimeSeries $aTimeSeries */
        foreach ($timeseriesCollection as $aTimeSeries) {
            $output[] = [
                $aTimeSeries->getId(),
                implode(',', $aTimeSeries->getDataPoints()),
            ];

        }

        $today = new \DateTime();
        $today = $today->format("Ymd");
        $fileName = "comp-engine-export-datapoints." . $today . ".csv";

        $fp = fopen(TimeSeriesDownloadService::CSV_STORAGE_DIRECTORY . $fileName, 'w');
        if (!$fp) {
            throw new \Exception("Failed to create CSV file.");
        }
        foreach ($output as $item) {
            fputcsv($fp, $item);
        }
        fclose($fp);

        return TimeSeriesDownloadService::CSV_STORAGE_DIRECTORY . $fileName;
    }

    public function generateTimeSeriesAsCsv() {
        $metadata = $this->generateTimeSeriesMetadataAsCsv();
        $datapoints = $this->generateTimeSeriesDatapointsAsCsv();

        $today = new \DateTime();
        $today = $today->format("Ymd");

        return $this->zipFiles("comp-engine-export." . $today, [$metadata, $datapoints]);
    }

    public function sendDownloadLink($email, $format) {
        global $configuration;
        $token = $this->generateDownloadToken($email, $format);
        $link = $configuration->get("server_domain_name") . "/time-series/export?token=" . $token;
        $mail = $this->generateDownloadLinkEmail($email, $link);

        $sg = new \SendGrid($configuration->get("sendgrid_api_key"));
        $sg->send($mail);
    }

    public function getDataFromToken($token) {
        $serializedData = $this->cacheAdaptor->getValue("time-series-" . $token);
        if (NULL === $serializedData){
            throw new \Exception("Invalid download token.");
        }

        return unserialize($serializedData);
    }

    public function getDirContents($dir) {
        $handle = opendir($dir);
        if (!$handle)
            return array();
        $contents = array();
        while ( $entry = readdir($handle) ) {
            if ( $entry=='.' || $entry=='..' )
                continue;

            $entry = $dir.DIRECTORY_SEPARATOR.$entry;
            if ( is_file($entry) ) {
                $contents[] = $entry;
            }
            else if ( is_dir($entry) ) {
                $contents = array_merge($contents, $this->getDirContents($entry));
            }
        }
        closedir($handle);

        return $contents;
    }

    public function getLatestTimeSeries($format) {
        if ($format === "csv") {
            $filePaths = $this->getDirContents(TimeSeriesDownloadService::CSV_STORAGE_DIRECTORY);
        } else {
            $filePaths = $this->getDirContents(TimeSeriesDownloadService::JSON_STORAGE_DIRECTORY);
        }

        if (empty($filePaths)) {
            throw new \Exception("No Time Series are currently available to download.");
        }

        $creationTimestamps = [];
        foreach ($filePaths as $filePath) {
            $creationTimestamps[] = [
                "filePath" => $filePath,
                "created" => filectime($filePath),
            ];
        }

        usort($creationTimestamps, function ($a, $b) {
            return $a['created'] - $b['created'];
        });

        return end($creationTimestamps)["filePath"];
    }

    public function downloadTimeSeries($token) {
        $data = $this->getDataFromToken($token);
        if ($data["format"] === "csv") {
            $latestTimeSeries = $this->getLatestTimeSeries("csv");
        } else {
            $latestTimeSeries = $this->getLatestTimeSeries("json");
        }

	    $quoted = sprintf('"%s"', addcslashes(basename($latestTimeSeries), '"\\'));
	    $size   = filesize($latestTimeSeries);

	    header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename=' . $quoted);
	    header('Content-Transfer-Encoding: binary');
	    header('Connection: Keep-Alive');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	    header('Pragma: public');
	    header('Content-Length: ' . $size);
        readfile($latestTimeSeries);
    }

}