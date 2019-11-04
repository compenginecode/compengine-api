<?php

namespace PresentationLayer\Routes\Compare\ComparisonKey\Convert\Post;

use Doctrine\ORM\EntityManager;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonService\ComparisonService;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonService\Exceptions\EEmptyTimeSeries;
use DomainLayer\TimeSeriesManagement\Ingestion\Converters\Exceptions\EParseConversionError;
use DomainLayer\TimeSeriesManagement\Ingestion\TimeSeriesIngester\Exceptions\ETruncationWarning;
use PresentationLayer\Routes\Compare\ComparisonKey\AbstractComparisonKeyInferredRoute;
use PresentationLayer\Routing\StatusCode\UnprocessableEntity;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\Compare\ComparisonKey\Convert\Post
 */
class Post extends AbstractComparisonKeyInferredRoute{

    /** $comparisonService
     *
     *  Service used to start and manage comparison jobs.
     *
     * @var ComparisonService
     */
    private $comparisonService;

    /** $exceptionMap
     *
     *  Maps internal exceptions to a naming convention acceptable by the front end.
     *
     * @var array
     */
    protected $exceptionMap = array(
        "DomainLayer\\TimeSeriesManagement\\Ingestion\\Converters\\Exceptions\\EParseConversionError" => "EParseConversionError",
        "DomainLayer\\TimeSeriesManagement\\Ingestion\\Converters\\SoxConverter\\Exceptions\\ESoxError" => "EAudioConversionError",
        "DomainLayer\\TimeSeriesManagement\\Comparison\\ComparisonService\\Exceptions\\EEmptyTimeSeries" => "EEmptyTimeSeries",
        "DomainLayer\\TimeSeriesManagement\\Ingestion\\TimeSeriesIngester\\Exceptions\\ETruncationWarning" => "ETruncationWarning",
    );

    /** __construct
     *
     *  Post constructor.
     *
     * @param EntityManager $entityManager
     * @param ComparisonService $comparisonService
     */
    public function __construct(EntityManager $entityManager, ComparisonService $comparisonService){
        parent::__construct($entityManager);
        $this->comparisonService = $comparisonService;
    }

    /** execute
     *
     *  Route execution.
     *
     */
    public function execute(){
        parent::execute();

        $request = $this->request->getBodyAsArray();

        $shouldIgnoreTruncationWarning = ! empty($request["shouldIgnoreTruncationWarning"]);

        try{
            $response = $this->comparisonService->startComparison(new ComparisonRequest($this->comparisonKey, $shouldIgnoreTruncationWarning));
            $this->response->setReturnBody(new JSONBody(["resultKey" => $response]));
        }

        catch (ETruncationWarning $exception) {
            $this->response->setStatusCode(new UnprocessableEntity());
            $this->response->setReturnBody(new JSONBody($this->wrapExceptionForFront(
                $exception,
                "The time series will be truncated."
            )));
        }

        catch (EParseConversionError $exception) {
            $this->response->setStatusCode(new UnprocessableEntity());
            $this->response->setReturnBody(new JSONBody($this->wrapExceptionForFront(
                $exception,
                "Could not detect the delimiter."
            )));
        }

        catch (EEmptyTimeSeries $exception) {
            $this->response->setStatusCode(new UnprocessableEntity());
            $this->response->setReturnBody(new JSONBody($this->wrapExceptionForFront(
                $exception,
                "Time series is empty."
            )));
        }

        catch (\Exception $exception) {
            throw $exception;
            $this->response->setStatusCode(new UnprocessableEntity());
            $this->response->setReturnBody(new JSONBody($this->wrapExceptionForFront(
                $exception,
                "Unknown comparison error."
            )));
        }
    }

}