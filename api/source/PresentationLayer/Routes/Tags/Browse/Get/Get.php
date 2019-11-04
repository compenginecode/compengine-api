<?php

namespace PresentationLayer\Routes\Tags\Browse\Get;

use DomainLayer\ORM\Tag\Repository\ITagRepository;
use PresentationLayer\Routes\EInvalidInputs;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Tags\Browse\Get
 */
class Get extends AbstractRoute
{
    /** tagRepository
     *
     *
     *
     * @var ITagRepository
     */
    private $tagRepository;

    /** __construct
     *
     *  Constructor
     *
     * @param ITagRepository $tagRepository
     */
    public function __construct(ITagRepository $tagRepository) {
        $this->tagRepository = $tagRepository;
    }

    public function execute() {
        $time1 = microtime(true);
        $tags = $this->tagRepository->listAll();
        if (isset($_GET["limit"]) && $_GET["limit"] >= 1) {
            $tags = array_slice($tags, 0, $_GET["limit"]);
        } else if (isset($_GET["limit"]) && !empty($_GET["limit"])) {
            throw new EInvalidInputs("Limit is invalid");
        }
        $time2 = microtime(true);

        $this->response->setReturnBody(new JSONBody([
            "tags" => $tags,
            "total" => count($tags),
            "time" => round($time2 - $time1, 3),
        ]));
    }
}
