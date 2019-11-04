<?php

namespace PresentationLayer\Routes\Timeseries\Metadata\Sources\Get;

use DomainLayer\ORM\Source\Source;
use DomainLayer\ORM\Source\Repository\ISourceRepository;
use DomainLayer\TimeSeriesManagement\Metadata\Sources\SourceRenderer\SourceRenderer;
use PresentationLayer\Routes\EInvalidInputs;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Timeseries\Metadata\Sources\Get
 */
class Get extends AbstractRoute{

    const MINIMUM_KEYWORD_LENGTH = 2;

    /** $sourceRepository
     *
     *  Repository interface for accessing sources.
     *
     * @var ISourceRepository
     */
    private $sourceRepository;

    /** $sourceRenderer
     *
     *  Service used to convert sources into JSON-capable
     *  arrays.
     *
     * @var SourceRenderer
     */
    private $sourceRenderer;

    /** __construct
     *
     *  Get constructor.
     *
     * @param ISourceRepository $sourceRepository
     * @param SourceRenderer $sourceRenderer
     */
    public function __construct(ISourceRepository $sourceRepository, SourceRenderer $sourceRenderer){
        $this->sourceRepository = $sourceRepository;
        $this->sourceRenderer = $sourceRenderer;
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
        foreach($this->sourceRepository->findByKeyword($_GET["keyword"]) as $aSource){
            /** @var $aSource Source */
            $renderedResults[] = $this->sourceRenderer->renderSource($aSource);
        }

        $this->response->setReturnBody(new JSONBody($renderedResults));
    }

}