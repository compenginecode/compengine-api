<?php

namespace DomainLayer\TimeSeriesManagement\Ingestion\BulkContributionService;

use ConfigurationLayer\ApplicationConfigFactory\ApplicationConfigFactory;
use Doctrine\ORM\EntityManager;
use DomainLayer\NotificationService\NotificationService;
use DomainLayer\ORM\Notification\Notification;
use DomainLayer\ORM\TimeSeries\BulkUploadedTimeSeries\BulkUploadedTimeSeries;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\TimeSeriesManagement\Ingestion\ContributionService\ContributionService;

/**
 * Class BulkContributionService
 * @package DomainLayer\TimeSeriesManagement\Ingestion\BulkContributionService
 */
class BulkContributionService
{
    /** entityManager
     *
     *
     *
     * @var EntityManager
     */
    private $entityManager;

    /** contributionService
     *
     *
     *
     * @var ContributionService
     */
    private $contributionService;


    /** contributeBulkUploadedTimeSeriesRequest
     *
     *
     *
     * @var ContributeBulkUploadedTimeSeriesRequest
     */
    private $contributeBulkUploadedTimeSeriesRequest;

	private $configuration;

    /** __construct
     *
     *  Constructor
     *
     * @param EntityManager $entityManager
     * @param ContributionService $contributionService
     * @param ContributeBulkUploadedTimeSeriesRequest $contributeBulkUploadedTimeSeriesRequest
     */
    public function __construct(EntityManager $entityManager, ContributionService $contributionService,
	    ContributeBulkUploadedTimeSeriesRequest $contributeBulkUploadedTimeSeriesRequest) {
        $this->entityManager = $entityManager;
        $this->contributionService = $contributionService;
        $this->contributeBulkUploadedTimeSeriesRequest = $contributeBulkUploadedTimeSeriesRequest;
	    $this->configuration = ApplicationConfigFactory::createFromFile(ROOT_PATH . "/private/configuration/configuration.ini", "default");
    }

    public function run() {
        $timeSeries = $this->entityManager->getRepository(BulkUploadedTimeSeries::class)
            ->findBy(["isApproved" => true, "isDenied" => false, "isSubmitted" => true]);

        array_walk($timeSeries, function (BulkUploadedTimeSeries $timeSeries) {
        	if (!$timeSeries->isProcessed()){
				$this->contributeBulkUploadedTimeSeriesRequest->fill($timeSeries);

				$persistedTimeSeries = $this->contributionService->contributeAndFlushTimeSeries(
					$this->contributeBulkUploadedTimeSeriesRequest,
					PersistedTimeSeries::ORIGIN_BULK_CONTRIBUTION
				);

				$persistedTimeSeries->approve();
				echo "Contributing time series from " . $timeSeries->getBulkUploadRequest()->getName() . "\n";
				$notification = new Notification(
					Notification::DAILY,
					$timeSeries->getBulkUploadRequest()->getName(),
					$timeSeries->getBulkUploadRequest()->getEmailAddress(),
					Notification::TIME_SERIES_APPROVED,
					[
						"fileName" => $timeSeries->getName(),
						"link" => $this->configuration->get("bulk_contribution_service_notification_link_prefix") . $persistedTimeSeries->getId(),
					]
				);

				$timeSeries->setIsProcessed(true);

				$this->entityManager->persist($notification);
				$this->entityManager->persist($timeSeries);
				$this->entityManager->flush();
			}

        });
    }
}
