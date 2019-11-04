<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\Converters\ExcelConverter;

use DomainLayer\TimeSeriesManagement\Ingestion\Converters\IConverter;

/**
 * Class ExcelConverter
 * @package DomainLayer\TimeSeriesManagement\Ingestion\Converters\ExcelConverter
 */
class ExcelConverter implements IConverter{

	/** convertToTimeSeries
	 *
	 * 	Converts the given file to an array of floats.
	 *  The array itself represents the time series data.
	 *
	 * @param $filePath
	 * @return array of float
	 */
	public function convertToTimeSeries($filePath){
		$timeSeries = [];
		$excelObj = \PHPExcel_IOFactory::load($filePath);

		$worksheet = $excelObj->setActiveSheetIndex(0);
		$lastRow = $worksheet->getHighestRow();
		for ($row = 1; $row <= $lastRow; $row++) {
			$cell = $worksheet->getCell("A" . $row);

			if (is_numeric($cell->getValue())){
				$timeSeries[] = (float)$cell->getValue();
			}
		}

		return $timeSeries;
	}

	/** getConversionType
	 *
	 * 	Returns a string that denotes the type of conversion
	 * 	process supplied.
	 *
	 * @return string
	 */
	public function getConversionType(){
		return "excel";
	}


}