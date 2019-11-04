<?php

namespace InfrastructureLayer\SitemapGenerator;

use Doctrine\Tests\Models\OneToOneSingleTableInheritance\Cat;
use DomainLayer\ORM\Category\Category;
use DomainLayer\ORM\Category\Repository\DatabaseCategoryRepository;
use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\ORM\Source\Repository\DatabaseSourceRepository;
use DomainLayer\ORM\Source\Source;
use DomainLayer\ORM\Tag\Repository\DatabaseTagRepository;
use DomainLayer\ORM\Tag\Tag;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository\DatabaseTimeSeriesRepository;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository\ITimeSeriesRepository;
use SitemapPHP\Sitemap;

/**
 * Class SitemapGenerator
 * @package InfrastructureLayer\SitemapGenerator
 */
class SitemapGenerator
{
    /** sitemap
     *
     *
     *
     * @var Sitemap
     */
    private $sitemap;

    /** siteAttributeRepository
     *
     *
     *
     * @var ISiteAttributeRepository
     */
    private $siteAttributeRepository;

    /** categoryRepository
     *
     *
     *
     * @var DatabaseCategoryRepository
     */
    private $categoryRepository;

    /** tagRepository
     *
     *
     *
     * @var DatabaseTagRepository
     */
    private $tagRepository;

    /** sourceRepository
     *
     *
     *
     * @var DatabaseSourceRepository
     */
    private $sourceRepository;

    /** timeSeriesRepository
     *
     *
     *
     * @var ITimeSeriesRepository
     */
    private $timeSeriesRepository;

    /** __construct
     *
     *  Constructor
     *
     * @param Sitemap $sitemap
     * @param ISiteAttributeRepository $siteAttributeRepository
     * @param DatabaseCategoryRepository $categoryRepository
     * @param DatabaseTagRepository $tagRepository
     * @param DatabaseSourceRepository $sourceRepository
     * @param ITimeSeriesRepository $timeSeriesRepository
     */
    public function __construct(Sitemap $sitemap, ISiteAttributeRepository $siteAttributeRepository, DatabaseCategoryRepository $categoryRepository, DatabaseTagRepository $tagRepository, DatabaseSourceRepository $sourceRepository, ITimeSeriesRepository $timeSeriesRepository) {
        $this->sitemap = $sitemap;
        $this->siteAttributeRepository = $siteAttributeRepository;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
        $this->sourceRepository = $sourceRepository;
        $this->timeSeriesRepository = $timeSeriesRepository;
    }

    public function run() {
        $this->sitemap->setPath(ROOT_PATH . "/public/sitemap/");
        $this->sitemap->addItem('/#!', '1.0', 'daily', 'Today');
        $this->addStaticPages();
        $this->addCategories();
        $this->addTags();
        $this->addSources();
        $this->addTimeSeries();

        $this->sitemap->createSitemapIndex('https://api.comp-engine.org/sitemap/', 'Today');
    }

    public function addCategories() {
        $categories = $this->categoryRepository->getRootCategories();
        array_walk($categories, function (Category $category) {
            $this->addCategory($category);
        });
    }

    public function addCategory(Category $category, $root = "/#!browse/categories/") {
        $loc = "{$root}{$category->getSlug()}/";
        $this->sitemap->addItem($loc, 0.5, 'daily', 'Today');
        array_walk($category->getChildren()->toArray(), function (Category $category) use ($loc) {
            $this->addCategory($category, $loc);
        });
    }

    public function addTags() {
        $tags = $this->tagRepository->findAll();
        array_walk($tags, function (Tag $tag) {
            $this->sitemap->addItem("/#!browse/tags/{$tag->getSlug()}/", 0.5, 'daily', 'Today');
        });
    }

    public function addSources() {
        $sources = $this->sourceRepository->findAll();
        array_walk($sources, function (Source $source) {
            $this->sitemap->addItem("/#!browse/sources/{$source->getSlug()}/", 0.5, 'daily', 'Today');
        });
    }

    public function addStaticPages() {
        $pages = $this->siteAttributeRepository->getStaticPageList();
        array_walk($pages, function ($page) {
            $page = preg_match("/\/$/", $page) ? $page : $page . "/";
            $this->sitemap->addItem($page, '0.7', 'daily', 'Today');
        });
    }

    public function addTimeSeries() {
        $timeSeries = $this->timeSeriesRepository->findAll();
        array_walk($timeSeries, function (PersistedTimeSeries $timeSeries) {
            $this->sitemap->addItem("/#!visualize/{$timeSeries->getId()}/", 0.4, 'weekly', 'Today');
        });
    }
}
