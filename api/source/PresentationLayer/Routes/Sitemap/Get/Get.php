<?php

namespace PresentationLayer\Routes\Sitemap\Get;

use InfrastructureLayer\SitemapGenerator\SitemapGenerator;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Sitemap\Get
 */
class Get extends AbstractRoute
{
    /** sitemapGenerator
     *
     *
     *
     * @var SitemapGenerator
     */
    private $sitemapGenerator;

    /** __construct
     *
     *  Constructor
     *
     * @param SitemapGenerator $sitemapGenerator
     */
    public function __construct(SitemapGenerator $sitemapGenerator) {
        $this->sitemapGenerator = $sitemapGenerator;
    }

    public function execute() {
        $this->sitemapGenerator->run();

        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
