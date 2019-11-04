<?php

namespace PresentationLayer\Routes\Compare\ComparisonKey\Status\Get;

use Doctrine\ORM\EntityManager;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonStatus\ComparisonStatus;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonStatusService\ComparisonStatusService;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonStatusService\Exceptions\EComparisonKeyNotFound;
use PresentationLayer\Routes\Compare\ComparisonKey\AbstractComparisonKeyInferredRoute;

/**
 * Class Get
 * @package PresentationLayer\Routes\Comparison\ComparisonKey\Status\Get
 */
class Get extends AbstractComparisonKeyInferredRoute{

    /** $comparisonStatusService
     *
     *  Service used for looking up the status of a
     *  comparison job.
     *
     * @var ComparisonStatusService
     */
    private $comparisonStatusService;

    /** sendSSEMessage
     *
     *  Sends a message through the output buffer compatible with the
     *  Server Side Events standard.
     *
     * @param $object
     */
    protected function sendSSEMessage($object){
        echo "data: " . json_encode($object) . "\n\n";
        ob_end_flush();
        flush();
    }

    /** __construct
     *
     *  Get constructor.
     *
     * @param EntityManager $entityManager
     * @param ComparisonStatusService $comparisonStatusService
     */
    public function __construct(EntityManager $entityManager, ComparisonStatusService $comparisonStatusService){
        parent::__construct($entityManager);

        $this->comparisonStatusService = $comparisonStatusService;
    }

    /** execute
     *
     *  Route execution.
     *
     */
    public function execute(){
        parent::execute();
        set_time_limit(15);

        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        header("Connection: keep-alive");
        header("Access-Control-Allow-Origin: *");

        /** We need to continuously send the status, even if it's the same. We cannot
         *  be sure that the message arrived or arrived when the client was ready to
         *  process it. **/
        while(TRUE){

            try{
                $status = $this->comparisonStatusService->getStatus($this->comparisonKey);

                if ($status->equals(ComparisonStatus::STATUS_CONVERSION_STARTED)){
                    $this->sendSSEMessage(array(
                        "code" => $status->chosenOption(),
                        "conversionType" => $status->getConversionType()
                    ));
                }else{
                    $this->sendSSEMessage(array(
                        "code" => $status->chosenOption()
                    ));
                }

            }catch (EComparisonKeyNotFound $exception){
                $this->sendSSEMessage("expired");
                exit();
            }finally {
                sleep(1);
            }
        }
    }

}