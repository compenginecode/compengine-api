<?php

namespace UnitTests\DomainLayer\TimeSeriesManagement\Ingestion\BulkContributionService;

use DomainLayer\ORM\BulkUploadRequest\BulkUploadRequest;
use DomainLayer\ORM\Notification\Notification;
use DomainLayer\ORM\TimeSeries\BulkUploadedTimeSeries\BulkUploadedTimeSeries;
use DomainLayer\TimeSeriesManagement\Ingestion\BulkContributionService\BulkContributionService;
use DomainLayer\TimeSeriesManagement\Ingestion\BulkContributionService\ContributeBulkUploadedTimeSeriesRequest;
use DomainLayer\TimeSeriesManagement\Ingestion\ContributionService\ContributionService;
use Mockery\MockInterface;

/**
 * Class BulkContributionServiceTest
 * @package UnitTests\DomainLayer\TimeSeriesManagement\Ingestion\BulkContributionService
 */
class BulkContributionServiceTest extends \PHPUnit_Framework_TestCase
{
    /** contributionService
     *
     *
     *
     * @var ContributionService|MockInterface
     */
    private $contributionService;

    /** contributeBulkUploadedTimeSeriesRequest
     *
     *
     *
     * @var ContributeBulkUploadedTimeSeriesRequest|MockInterface
     */
    private $contributeBulkUploadedTimeSeriesRequest;

    /** bulkContributionService
     *
     *
     *
     * @var BulkContributionService
     */
    private $bulkContributionService;

    public function setUp() {
        global $entityManager;
        $this->contributionService = \Mockery::mock(ContributionService::class);
        $this->contributionService->shouldReceive(["contributeAndFlushTimeSeries" => null]);
        $this->contributeBulkUploadedTimeSeriesRequest = \Mockery::mock(ContributeBulkUploadedTimeSeriesRequest::class)->makePartial();
        $this->bulkContributionService = new BulkContributionService($entityManager, $this->contributionService, $this->contributeBulkUploadedTimeSeriesRequest);
    }

    public function tearDown() {
        \Mockery::close();
    }

    /** test_running_the_bulk_contribution_cron_job
     *
     *  How it should work:
     *  Any approved, submitted time series should have its feature vector, etc generated and be converted to a PersistedTimeSeries
     *  A notification should be queued to send to the contributor in the daily email run.
     *  The BulkUploadedTimeSeries should now be deleted.
     *
     */
    public function test_running_the_bulk_contribution_cron_job() {
        global $entityManager;
        $bulkUploadRequest = $this->makeBulkUploadRequest();
        $bulkUploadedTimeSeries = $this->makeBulkUploadedTimeSeries($bulkUploadRequest);
        $bulkUploadedTimeSeries->setAsApproved();
        $bulkUploadedTimeSeries->setAsSubmitted();
        $entityManager->persist($bulkUploadedTimeSeries);
        $entityManager->flush();
        $entityManager->refresh($bulkUploadedTimeSeries);
        $id = $bulkUploadedTimeSeries->getId();

        $this->bulkContributionService->run();

        $matchingNotifications = $entityManager->getRepository(Notification::class)->createQueryBuilder("n")
            ->where("n.body LIKE :id")
            ->setParameter("id", "%{$id}%")
            ->getQuery()->execute();

        $this->assertEquals(1, count($matchingNotifications));

        $entityManager->remove($matchingNotifications[0]);
        $entityManager->remove($bulkUploadRequest);
        $entityManager->flush();
    }

    public function makeBulkUploadRequest() {
        global $entityManager;
        $approvalToken = "ubgi753qyr3...2hgoq8790923";
        $exchangeToken = "p98347n5v9...1c2y45brndkhfwexgrew";
        $bulkUploadRequest = new BulkUploadRequest("Jim", "jim@example.org", "", "", $approvalToken);
        $bulkUploadRequest->setApprovedAt(new \DateTime());
        $bulkUploadRequest->setExchangeToken($exchangeToken);
        $entityManager->persist($bulkUploadRequest);
        $entityManager->flush();
        return $bulkUploadRequest;
    }

    public function makeBulkUploadedTimeSeries($bulkUploadRequest) {
        global $entityManager;
        $bulkUploadedTimeSeries = new BulkUploadedTimeSeries([], 0);
        $bulkUploadedTimeSeries->setBulkUploadRequest($bulkUploadRequest);
        $entityManager->persist($bulkUploadedTimeSeries);
        $entityManager->flush();
        $entityManager->refresh($bulkUploadedTimeSeries);
        return $bulkUploadedTimeSeries;
    }
}
