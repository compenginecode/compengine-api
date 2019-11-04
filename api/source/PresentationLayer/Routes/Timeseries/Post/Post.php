<?php

namespace PresentationLayer\Routes\Timeseries\Post;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\TimeSeriesManagement\Ingestion\ContributionService\ContributionService;
use PresentationLayer\Routes\Timeseries\Post\Requests\ContributeTimeSeriesWebRequest;
use PresentationLayer\Routes\TransactionRoute;
use PresentationLayer\Routing\StatusCode\UnprocessableEntity;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\Timeseries\Post\Post
 */
class Post extends TransactionRoute{

    private $contributeTimeSeriesWebRequest;

    private $contributionService;

    /** $exceptionMap
     *
     *  Maps internal exceptions to a naming convention acceptable by the front end.
     *
     * @var array
     */
    protected $exceptionMap = [
        "Doctrine\\DBAL\\Exception\\UniqueConstraintViolationException" => "ETimeSeriesNameInUse"
    ];

    /** __construct
     *
     *  Post constructor.
     *
     * @param EntityManager $entityManager
     * @param ContributeTimeSeriesWebRequest $contributeTimeSeriesWebRequest
     * @param ContributionService $contributionService
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, ContributeTimeSeriesWebRequest $contributeTimeSeriesWebRequest,
        ContributionService $contributionService){

        parent::__construct($entityManager);
        $this->contributeTimeSeriesWebRequest = $contributeTimeSeriesWebRequest;
        $this->contributionService = $contributionService;
    }

    /** execute
     *
     *  Route execution.
     *
     */
    public function execute(){
        $postBody = (NULL === $this->request->getBodyAsArray()) ? [] : $this->request->getBodyAsArray();

        $this->contributeTimeSeriesWebRequest->populateRequest($postBody);

        try{
            $timeSeries = $this->contributionService->contributeAndFlushTimeSeries(
            	$this->contributeTimeSeriesWebRequest,
				PersistedTimeSeries::ORIGIN_INDIVIDUAL_CONTRIBUTION
			);

            $this->response->setReturnBody(new JSONBody(array(
                "message" => "success",
                "uri" => $timeSeries->getId()
            )));

        }catch(UniqueConstraintViolationException $exception){
            $this->response->setStatusCode(new UnprocessableEntity());
            $this->response->setReturnBody(new JSONBody($this->wrapExceptionForFront(
                $exception,
                "Time series name is already in use."
            )));
        }
    }

}