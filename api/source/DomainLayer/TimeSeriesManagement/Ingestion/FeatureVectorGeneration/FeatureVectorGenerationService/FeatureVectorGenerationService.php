<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorGenerationService;

use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorGenerationService\Exceptions\EManifestNotAdheredTo;
use DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\MappingManifest\MappingManifest;

/**
 * Class FeatureVectorGenerationService
 * @package DomainLayer\TimeSeriesManagement\Ingestion\FeatureVectorGeneration\FeatureVectorGenerationService
 */
class FeatureVectorGenerationService {

    /**
     * @param array $timeSeriesDataPoints
     * @param MappingManifest $mappingManifest
     * @return FeatureVector
     * @throws EManifestNotAdheredTo
     * @throws \Exception
     */
    public function generateFeatureVector(array $timeSeriesDataPoints, MappingManifest $mappingManifest){
        $output = [];
        $fullPath = ROOT_PATH . "/private/temp/" . time() . md5(rand()) . ".txt";
        $input = implode(PHP_EOL, $timeSeriesDataPoints);

        file_put_contents($fullPath, $input);
        exec($mappingManifest->getGeneratorScriptPath() . " " . $fullPath, $output);
        unlink($fullPath);

        if (1 === count($output)){
            throw new \Exception("Feature vector program failed: " . $output[0]);
        }

		$index = 0;
		$featureVector = new FeatureVector();
		$returnedMappingIds = [];
		foreach($output as $aLine){
			if (!empty($aLine)){
				$returnedMappingIds[] = "m" . (string)$index;
				$featureVector->addElementValue("m" . $index, (float)str_getcsv($aLine)[0]);
				$index++;
			}
		}

        /** Now we test to make sure the manifest was met. We compute the difference between
         * 	what ids we got and what we expected. If the difference has a length greater than
         * 	zero then we were missing one or supplied an extra one (at least). */
        $differenceA = array_diff($returnedMappingIds, $mappingManifest->featureElementMappingIds());
        $differenceB = array_diff($mappingManifest->featureElementMappingIds(), $returnedMappingIds);
        $difference = array_merge($differenceA, $differenceB);

        if (0 != count($difference)){
            throw new EManifestNotAdheredTo($returnedMappingIds, $mappingManifest->featureElementMappingIds());
        }

        return $featureVector;
    }

}