<?php

namespace DomainLayer\TimeSeriesManagement\Comparison\Family;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\ORM\FeatureVectorIndex\FeatureVectorIndex;
use DomainLayer\ORM\LSHOptions\LSHOptions;
use Elasticsearch\Client;
use InfrastructureLayer\ElasticSearch\ElasticSearch;

/**
 * Class CreateFamilyService
 * @package DomainLayer\TimeSeriesManagement\Comparison\Family
 */
class CreateFamilyService {

    private $elasticSearch;

    private $entityManager;

    protected function generateFeatureVectorFamily($name, $description, $generatorScriptPath,
                                                   LSHOptions $commonIndexLSHOptions, LSHOptions $syntheticIndexLSHOptions, LSHOptions $realIndexLSHOptions){

        $family = new FeatureVectorFamily(
            $name,
            $description,
            $generatorScriptPath,
            $commonIndexLSHOptions,
            $syntheticIndexLSHOptions,
            $realIndexLSHOptions
        );

		$featureVectorDescriptors = array(
			"CO_Embed2_Basic_tau.incircle_1",
			"CO_Embed2_Basic_tau.incircle_2",
			"FC_LocalSimple_mean1.taures",
			"DN_HistogramMode_10",
			"SY_StdNthDer_1",
			"AC_9",
			"SB_MotifTwo_mean.hhh",
			"CO_FirstMin_ac",
			"DN_OutlierInclude_abs_001.mdrmd",
			"CO_trev_1.num",
			"SY_SpreadRandomLocal_50_100.meantaul",
			"SC_FluctAnal_2_rsrangefit_50_1_logi.prop_r1",
			"PH_Walker_prop_01.sw_propcross",
			"SY_SpreadRandomLocal_ac2_100.meantaul",
			"FC_LocalSimple_lfit.taures",
			"EN_SampEn_5_03.sampen1"
		);

        $i = 0;
        foreach($featureVectorDescriptors as $aDescriptor){
            $family->descriptors()->addDescriptor($aDescriptor, "m" . $i);
            $i++;
        }

        $family->prepareIndices();

        return $family;
    }

    protected function generateIndices(FeatureVectorFamily $featureVectorFamily){
        $this->elasticSearch->createIndexForFeatureVectorFamily($featureVectorFamily);
    }

    public function __construct(ElasticSearch $elasticSearch, EntityManager $entityManager) {
        $this->elasticSearch = $elasticSearch;
        $this->entityManager = $entityManager;
    }

    public function createNewFeatureVectorFamily($name, $description, $generatorScriptPath,
                                                 LSHOptions $commonIndexLSHOptions, LSHOptions $syntheticIndexLSHOptions, LSHOptions $realIndexLSHOptions){

        $featureVectorFamily = $this->generateFeatureVectorFamily(
            $name,
            $description,
            $generatorScriptPath,
            $commonIndexLSHOptions,
            $syntheticIndexLSHOptions,
            $realIndexLSHOptions
        );

        $this->entityManager->persist($featureVectorFamily);
        $this->entityManager->flush();
        $this->entityManager->refresh($featureVectorFamily);

        $this->generateIndices($featureVectorFamily);

        return $featureVectorFamily;
    }

}