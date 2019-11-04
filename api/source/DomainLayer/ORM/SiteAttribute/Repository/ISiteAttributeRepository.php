<?php

namespace DomainLayer\ORM\SiteAttribute\Repository;

use DomainLayer\ORM\FeatureVectorFamily\FeatureVectorFamily;
use DomainLayer\ORM\SiteAttribute\SiteAttribute;

/**
 * Interface ISiteAttributeRepository
 * @package DomainLayer\ORM\SiteAttribute\Repository
 */
interface ISiteAttributeRepository {

	/** getComparisonResultCacheTime
	 *
	 * 	Returns the time, in seconds, that comparison results are stored in memory.
	 *
	 * @return int
	 */
	public function getComparisonResultCacheTime();

	/** getCurrentFeatureVectorFamily
	 *
	 * 	Returns the currently used feature vector family.
	 *
	 * @return FeatureVectorFamily
	 */
	public function getCurrentFeatureVectorFamily();

    /** getStaticPageList
     *
     *  Returns an array of the static frontend pages.
     *  Used for generating sitemap.
     *
     * @return array
     */
    public function getStaticPageList();

    /**
     * @return mixed
     */
    public function getTotalDataPoints();
}