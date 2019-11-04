<?php

namespace PresentationLayer\Routes\Sources\Browse\Get;

use DomainLayer\ORM\Source\Repository\ISourceRepository;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Sources\Browse\Get
 */
class Get extends AbstractRoute
{
    /** sourceRepository
     *
     *
     *
     * @var ISourceRepository
     */
    private $sourceRepository;

    /** __construct
     *
     *  Constructor
     *
     * @param ISourceRepository $sourceRepository
     */
    public function __construct(ISourceRepository $sourceRepository) {
        $this->sourceRepository = $sourceRepository;
    }

    public function execute() {
        $time1 = microtime(true);
        $sources = $this->sourceRepository->listAll();
        $time2 = microtime(true);

        $sources = array_values(array_filter($sources, function ($source) {
            return ((int) $source["total"]) > 0;
        }));

        $this->response->setReturnBody(new JSONBody([
            "sources" => $sources,
            "total" => count($sources),
            "time" => round($time2 - $time1, 3),
        ]));
    }
}
