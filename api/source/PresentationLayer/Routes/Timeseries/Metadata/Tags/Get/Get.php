<?php

namespace PresentationLayer\Routes\Timeseries\Metadata\Tags\Get;

use DomainLayer\ORM\Tag\Tag;
use DomainLayer\ORM\Tag\Repository\ITagRepository;
use DomainLayer\TimeSeriesManagement\Metadata\Tags\TagRenderer\TagRenderer;
use PresentationLayer\Routes\EInvalidInputs;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Timeseries\Metadata\Tags\Get
 */
class Get extends AbstractRoute{

    const MINIMUM_KEYWORD_LENGTH = 1;

    /** $tagRepository
     *
     *  Repository interface for accessing tags.
     *
     * @var ITagRepository
     */
    private $tagRepository;

    /** $tagRenderer
     *
     *  Service used to convert tags into JSON-capable
     *  arrays.
     *
     * @var TagRenderer
     */
    private $tagRenderer;

    /** __construct
     *
     *  Get constructor.
     *
     * @param ITagRepository $tagRepository
     * @param TagRenderer $tagRenderer
     */
    public function __construct(ITagRepository $tagRepository, TagRenderer $tagRenderer){
        $this->tagRepository = $tagRepository;
        $this->tagRenderer = $tagRenderer;
    }

    /** execute
     *
     *  Route execution.
     *
     */
    public function execute(){
        /** Ensure we have a keyword */
        if (!isset($_GET["keyword"])) {
            throw new EInvalidInputs("keyword parameter is required.");
        }

        /** Ensure we have a correct minimum length */
        if (strlen($_GET["keyword"]) <= self::MINIMUM_KEYWORD_LENGTH){
            throw new EInvalidInputs("keyword must be greater than " . self::MINIMUM_KEYWORD_LENGTH . " characters in length.");
        }

        $renderedResults = array();
        foreach($this->tagRepository->findByKeyword($_GET["keyword"]) as $aTag){
            /** @var $aTag Tag */
            $renderedResults[] = $this->tagRenderer->renderTag($aTag);
        }

        $this->response->setReturnBody(new JSONBody($renderedResults));
    }

}