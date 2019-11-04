<?php

namespace DomainLayer\TimeSeriesManagement\SearchResultsDownloadService;

use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository\ITimeSeriesRepository;
use DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService\TimeSeriesDownloadService;

/**
 * Class SearchResultsDownloadService
 * @package DomainLayer\TimeSeriesManagement\SearchResultsDownloadService
 */
class SearchResultsDownloadService {

	private $timeSeriesRepository;

	protected function retrieveAllTimeSeries(array $ids){
		return $this->timeSeriesRepository->findManyByIds(...$ids);
	}

	protected function createCSV($data, $fullPath){
		$fp = fopen($fullPath, 'w');
		if (!$fp) {
			throw new \Exception("Failed to create CSV file $fullPath.");
		}
		foreach ($data as $item) {
			fputcsv($fp, $item);
		}
		fclose($fp);
	}

	protected function zipFiles($fullPath, array $files) {
		$archive = new \ZipArchive();

		if ($archive->open($fullPath, \ZipArchive::CREATE) === FALSE) {
			throw new \Exception("Failed to create zip archive $fullPath.");
		} else {
			foreach ($files as $file) {
				$archive->addFile($file, basename($file));
			}
			$archive->close();

			foreach ($files as $file) {
				unlink($file);
			}

			return $fullPath;
		}
	}

	public function __construct(ITimeSeriesRepository $timeSeriesRepository){
		$this->timeSeriesRepository = $timeSeriesRepository;
	}

	public function exportToCSV($timeSeriesIds){
		$metaData = [[
			"timeseries_id", "timestamp_created", "source", "category", "contributor", "name", "description",
			"sampling_unit", "sampling_rate"
		]];

		$dataPoints = [["timeseries_id", "datapoints"]];

		/** @var PersistedTimeSeries $aTimeSeries */
		foreach ($this->retrieveAllTimeSeries($timeSeriesIds) as $aTimeSeries) {
			$metaData[] = [
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

			$dataPoints[] = array(
				$aTimeSeries->getId(),
				implode(',', $aTimeSeries->getDataPoints()),
			);
		}

		$token = substr(md5(time()), 0, 5);
		$metaDataFileName = ROOT_PATH . "/private/temp/" . "comp-engine-export-metadata-$token.csv";
		$dataPointsFileName = ROOT_PATH . "/private/temp/" . "comp-engine-export-datapoints-$token.csv";
		$zipFileName =  ROOT_PATH . "/private/temp/" . "comp-engine-export-$token.zip";

		try{
			$this->createCSV($metaData, $metaDataFileName);
			$this->createCSV($dataPoints, $dataPointsFileName);
			$this->zipFiles($zipFileName, [$metaDataFileName, $dataPointsFileName]);

			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: public");
			header("Content-Description: File Transfer");
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"Timeseries.zip\"");
			header("Content-Transfer-Encoding: binary");

			readfile($zipFileName);
		}finally{
			@unlink($zipFileName);
		}
		die();
	}

	public function exportToJSON($timeSeriesIds){
		$output = [];
		foreach ($this->retrieveAllTimeSeries($timeSeriesIds) as $aTimeSeries) {
			/** @var PersistedTimeSeries $aTimeSeries */
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

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"Timeseries.json\"");
		header("Content-Transfer-Encoding: binary");

		echo json_encode($output);
		die();
	}

}