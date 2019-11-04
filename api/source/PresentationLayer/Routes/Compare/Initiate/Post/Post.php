<?php

namespace PresentationLayer\Routes\Compare\Initiate\Post;

use Doctrine\ORM\EntityManager;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonStatus\ComparisonStatus;
use DomainLayer\TimeSeriesManagement\Comparison\ComparisonStatusService\ComparisonStatusService;
use DomainLayer\TimeSeriesManagement\Comparison\UploadService\UploadService;
use PresentationLayer\Routes\TransactionRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\Store\Post
 */
class Post extends TransactionRoute{

    /** $uploadService
     *
     *  Service used to manage the upload process.
     *
     * @var UploadService
     */
    private $uploadService;

    /** $comparisonStatusService
     *
     *  Service used to set the status of a comparison job.
     *
     * @var ComparisonStatusService
     */
    private $comparisonStatusService;

    /** __construct
     *
     *  Post constructor.
     *
     * @param EntityManager $entityManager
     * @param UploadService $uploadService
     * @param ComparisonStatusService $comparisonStatusService
     */
    public function __construct(EntityManager $entityManager, UploadService $uploadService,
        ComparisonStatusService $comparisonStatusService){

        parent::__construct($entityManager);

        $this->uploadService = $uploadService;
        $this->comparisonStatusService = $comparisonStatusService;
    }

    /** execute
     *
     *  Route execution.
     *
     */
    public function execute(){
        $comparisonKey = $this->uploadService->getComparisonKey("file");
        $data = $this->uploadService->getFileData($comparisonKey);
//        var_dump($data);die();
        $dataPoints = count($data);

        /** The job doesn't start until the user requests it to start. Happens though a different route */
        $this->comparisonStatusService->updateStatus($comparisonKey, ComparisonStatus::idle());

        $this->response->setReturnBody(new JSONBody([
            "comparisonKey" => $comparisonKey,
            "dataPoints" => $data,
        ]));
    }

}