<?php

namespace UnitTests\DomainLayer\NotificationService;

use DomainLayer\NotificationService\NotificationRenderer;
use DomainLayer\NotificationService\NotificationService;
use DomainLayer\ORM\Notification\Notification;
use DomainLayer\ORM\Notification\Repository\INotificationRepository;
use InfrastructureLayer\EmailGateway\IEmailGateway;
use InfrastructureLayer\EmailGateway\SendGridEmailGateway\SendGridEmailGateway;
use InfrastructureLayer\EmailTemplate\EmailTemplate;
use Mockery\MockInterface;

/**
 * Class NotificationServiceTest
 * @package UnitTests\DomainLayer\NotificationService
 */
class NotificationServiceTest extends \PHPUnit_Framework_TestCase
{
    /** notificationRepository
     *
     *
     *
     * @var INotificationRepository|MockInterface
     */
    private $notificationRepository;

    /** emailGateway
     *
     *
     *
     * @var IEmailGateway|MockInterface
     */
    private $emailGateway;

    /** emailTemplate
     *
     *
     *
     * @var EmailTemplate|MockInterface
     */
    private $emailTemplate;

    /** notificationRenderer
     *
     *
     *
     * @var NotificationRenderer|MockInterface
     */
    private $notificationRenderer;

    public function setUp() {
        global $container;
        $this->notificationRepository = $container->get(INotificationRepository::class);
        $this->emailGateway = \Mockery::mock(SendGridEmailGateway::class);
        $this->emailTemplate = \Mockery::mock(EmailTemplate::class)->shouldReceive("generateTemplate")->getMock();
        $this->notificationRenderer = \Mockery::mock(NotificationRenderer::class);
    }

    public function tearDown() {
        \Mockery::close();
    }

    public function test_running_daily_notifications_cron_job() {
        global $entityManager;
        $testNotification = new Notification(Notification::DAILY, "Jimmy", "jimmy@example.org", Notification::TIME_SERIES_APPROVED, []);
        $entityManager->persist($testNotification);
        $entityManager->flush();

        /**
         * Check email is sent as expected.
         */
        $this->emailGateway->shouldReceive("sendEmail")->atLeast()->once();
        $notificationService = new NotificationService($this->notificationRepository, $this->emailGateway, $this->emailTemplate, $this->notificationRenderer, $entityManager);
        $notificationService->sendDailyNotifications();

        /**
         * Notification should be deleted now that the daily notification service has been run.
         * Id will be null if it is deleted.
         */
        $this->assertNull($testNotification->getId());
    }

    public function test_running_weekly_notifications_cron_job() {
        global $entityManager;
        $testNotification = new Notification(Notification::WEEKLY, "Jimmy", "jimmy@example.org", Notification::NEW_MESSAGE, []);
        $entityManager->persist($testNotification);
        $entityManager->flush();

        /**
         * Check email is sent as expected.
         */
        $this->emailGateway->shouldReceive("sendEmail")->atLeast()->once();
        $notificationService = new NotificationService($this->notificationRepository, $this->emailGateway, $this->emailTemplate, $this->notificationRenderer, $entityManager);
        $notificationService->sendWeeklyNotifications();

        /**
         * Notification should be deleted now that the daily notification service has been run.
         * Id will be null if it is deleted.
         */
        $this->assertNull($testNotification->getId());
    }
}
