<?php

namespace PresentationLayer\Routes\Admin\TimeSeries\Batches\Get;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\BulkUploadRequest\BulkUploadRequest;
use DomainLayer\ORM\TimeSeries\BulkUploadedTimeSeries\BulkUploadedTimeSeries;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Admin\TimeSeries\Batches\Get
 */
class Get extends UserInferredRoute
{

    /** __construct
     *
     *  Constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager) {
		parent::__construct($sessionService, $entityManager);
    }

    public function execute() {
		parent::execute();

		$bulkUploadedTimeSeriesRepository = $this->entityManager->getRepository(BulkUploadedTimeSeries::class);
        $bulkUploadedTimeSeries = $bulkUploadedTimeSeriesRepository->findAll();

        $bulkUploadedTimeSeries = array_reduce($bulkUploadedTimeSeries, function ($carry, BulkUploadedTimeSeries $bulkUploadedTimeSeries) {
            $bulkUploadRequestId = $bulkUploadedTimeSeries->getBulkUploadRequest()->getId();
            if (! isset($carry[$bulkUploadRequestId])) {
                $carry[$bulkUploadRequestId] = $this->renderBulkUploadRequestFromBulkUploadedTimeSeries($bulkUploadedTimeSeries);
            }
            $carry[$bulkUploadRequestId]['files'][] = $this->renderBulkUploadedTimeSeries($bulkUploadedTimeSeries);
            $carry[$bulkUploadRequestId]['fileCount'] = count($carry[$bulkUploadRequestId]['files']);

            return $carry;
        }, []);
        $bulkUploadedTimeSeries = array_values($bulkUploadedTimeSeries);

        $this->response->setReturnBody(new JSONBody(compact("bulkUploadedTimeSeries")));
    }

    /** renderBulkUploadRequest
     *
     *
     *
     * @param BulkUploadedTimeSeries $bulkUploadedTimeSeries
     * @return array
     */
    private function renderBulkUploadRequestFromBulkUploadedTimeSeries(BulkUploadedTimeSeries $bulkUploadedTimeSeries) {
    	$status = 'pending';
    	if ($bulkUploadedTimeSeries->isApproved()){
    		$status = 'approved';
		}

		if ($bulkUploadedTimeSeries->isDenied()){
			$status = 'rejected';
		}

        return [
            'id' => $bulkUploadedTimeSeries->getBulkUploadRequest()->getId(),
            'uploadedAt' => $bulkUploadedTimeSeries->timestampCreated()->format('Y-m-d H:i:s'),
            'contributor' => $bulkUploadedTimeSeries->getBulkUploadRequest()->getName(),
            'category' => $bulkUploadedTimeSeries->getCategory()->getName(),
            'source' => $bulkUploadedTimeSeries->getSource() ? $bulkUploadedTimeSeries->getSource()->getName() : null,
            'tags' => $bulkUploadedTimeSeries->getTagNames(),
            'samplingRate' => $bulkUploadedTimeSeries->getSamplingInformation()->getSamplingRate(),
            'samplingUnit' => $bulkUploadedTimeSeries->getSamplingInformation()->getSamplingUnit(),
            'fileCount' => 0,
			'status' => $status,
            'files' => [],
        ];
    }

    /** renderBulkUploadedTimeSeries
     *
     *
     *
     * @param BulkUploadedTimeSeries $bulkUploadedTimeSeries
     * @return array
     */
    private function renderBulkUploadedTimeSeries(BulkUploadedTimeSeries $bulkUploadedTimeSeries) {
        return [
            "id" => $bulkUploadedTimeSeries->getId(),
            "name" => $bulkUploadedTimeSeries->getName(),
            "uploadedAt" => $bulkUploadedTimeSeries->timestampCreated()->format("Y-m-d H:i:s"),
        ];
    }
}
