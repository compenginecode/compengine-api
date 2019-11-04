<?php

namespace PresentationLayer\Routes\Admin\Dashboard\Get;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\BulkUploadRequest\BulkUploadRequest;
use DomainLayer\ORM\TimeSeries\BulkUploadedTimeSeries\BulkUploadedTimeSeries;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Get
 * @package PresentationLayer\Routes\Admin\Diagnostics\Get
 */
class Get extends AbstractRoute{

	private $entityManager;

	protected function totalTimeSeries(){
		return (int)$this->entityManager->getRepository(PersistedTimeSeries::class)
			->createQueryBuilder('pts')
			->select('count(pts.id)')
			->getQuery()->getResult()[0][1];
	}

	protected function totalIndividuallyUploadedTimeSeries(){
		return (int)$this->entityManager->getRepository(PersistedTimeSeries::class)
			->createQueryBuilder('pts')
			->select('count(pts.id)')
			->where('pts.origin = :pOrigin')
			->setParameter('pOrigin', PersistedTimeSeries::ORIGIN_INDIVIDUAL_CONTRIBUTION)
			->getQuery()->getResult()[0][1];
	}

	protected function totalBulkUploadedTimeSeries(){
		return (int)$this->entityManager->getRepository(PersistedTimeSeries::class)
			->createQueryBuilder('pts')
			->select('count(pts.id)')
			->where('pts.origin = :pOrigin')
			->setParameter('pOrigin', PersistedTimeSeries::ORIGIN_BULK_CONTRIBUTION)
			->getQuery()->getResult()[0][1];
	}

	/**
	 * Get constructor.
	 * @param EntityManager $entityManager
	 */
    public function __construct(EntityManager $entityManager){
    	$this->entityManager = $entityManager;
	}

    /** execute
     *
     *  Route execution.
     *
     */
    public function execute(){
    	$response = array(
    		'totalTimeSeries' => $this->totalTimeSeries(),
			'totalIndividuallyUploadedTimeSeries' => $this->totalIndividuallyUploadedTimeSeries(),
			'totalBulkUploadedTimeSeries' => $this->totalBulkUploadedTimeSeries(),
		);

        $this->response->setReturnBody(new JSONBody($response));
    }

}