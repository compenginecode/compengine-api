<?php

namespace DomainLayer\ORM\SiteAttribute\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\ORM\FeatureVectorFamily\Repository\IFeatureVectorFamilyRepository;
use DomainLayer\ORM\SiteAttribute\Repository\Exceptions\EMalformedSiteAttributeValue;
use DomainLayer\ORM\SiteAttribute\Repository\Exceptions\ESiteAttributeMissing;
use DomainLayer\ORM\SiteAttribute\SiteAttribute;

/**
 * Class FullSiteAttributeRepository
 * @package DomainLayer\ORM\SiteAttribute\Repository
 */
class FullSiteAttributeRepository

	implements ISiteAttributeRepository{

	/** $databaseSiteAttributeRepository
	 *
	 * 	Repository to access site attributes.
	 *
	 * @var DatabaseSiteAttributeRepository
	 */
	private $databaseSiteAttributeRepository;

	/** $featureVectorFamilyRepository
	 *
	 * 	Repository to access feature vector families.
	 *
	 * @var IFeatureVectorFamilyRepository
	 */
	private $featureVectorFamilyRepository;

	/** __construct
	 *
	 * 	FullSiteAttributeRepository constructor.
	 *
	 * @param DatabaseSiteAttributeRepository $databaseSiteAttributeRepository
	 * @param IFeatureVectorFamilyRepository $featureVectorFamilyRepository
	 */
	public function __construct(DatabaseSiteAttributeRepository $databaseSiteAttributeRepository,
		IFeatureVectorFamilyRepository $featureVectorFamilyRepository){

		$this->databaseSiteAttributeRepository = $databaseSiteAttributeRepository;
		$this->featureVectorFamilyRepository = $featureVectorFamilyRepository;
	}


	/** getSiteAttribute
	 *
	 * 	Returns the site attribute object defined by its key. If not found,
	 *  ESiteAttributeMissing is thrown.
	 *
	 * @param $key
	 * @return SiteAttribute
	 * @throws ESiteAttributeMissing
	 */
	protected function getSiteAttribute($key){
		$attribute = $this->databaseSiteAttributeRepository->findOneBy(["key" => $key]);
		if (NULL === $attribute){
			throw new ESiteAttributeMissing($key);
		}

		return $attribute;
	}

	/** getComparisonResultCacheTime
	 *
	 * 	Returns the time, in seconds, that comparison results are stored in memory.
	 *
	 * @return int
	 */
	public function getComparisonResultCacheTime(){
		return (int)$this->getSiteAttribute("comparisonResultCacheTime")->getValue();
	}

	/** getCurrentFeatureVectorFamily
	 *
	 * 	Returns the currently used feature vector family.
	 *
	 * @return FeatureVectorFamily
	 * @throws EMalformedSiteAttributeValue
	 */
	public function getCurrentFeatureVectorFamily(){
		$fvfId = $this->getSiteAttribute("currentFeatureVectorFamily")->getValue();
		$fvfObj = $this->featureVectorFamilyRepository->findById($fvfId);

		if (NULL === $fvfObj){
			throw new EMalformedSiteAttributeValue("currentFeatureVectorFamily", "The feature vector family cannot be found with ID '$fvfId'.");
		}

		return $fvfObj;
	}

    /** getStaticPageList
     *
     *  Returns an array of the static frontend pages.
     *  Used for generating sitemap.
     *
     * @return mixed
     */
    public function getStaticPageList() {
        return json_decode($this->getSiteAttribute("staticPageList")->getValue(), true);
    }

    public function getTotalDataPoints() {
        /** @var SiteAttribute $attribute */
        $attribute = $attribute = $this->databaseSiteAttributeRepository->findOneBy(["key" => "totalDataPoints"]);
        if (! $attribute) {
            return 0;
        }
        return $attribute->getValue();
    }

}