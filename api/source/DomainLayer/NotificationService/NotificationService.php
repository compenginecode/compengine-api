<?php

namespace DomainLayer\NotificationService;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Notification\Notification;
use DomainLayer\ORM\Notification\Repository\INotificationRepository;
use InfrastructureLayer\EmailGateway\IEmailGateway;
use InfrastructureLayer\EmailTemplate\EmailTemplate;

/**
 * Class NotificationService
 * @package DomainLayer\NotificationService
 */
class NotificationService
{
    /** notificationRepository
     *
     *
     *
     * @var INotificationRepository
     */
    private $notificationRepository;

    /** emailGateway
     *
     *
     *
     * @var IEmailGateway
     */
    private $emailGateway;

    /** emailTemplate
     *
     *
     *
     * @var EmailTemplate
     */
    private $emailTemplate;

    /** notificationRenderer
     *
     *
     *
     * @var NotificationRenderer
     */
    private $notificationRenderer;

    /** entityManager
     *
     *
     *
     * @var EntityManager
     */
    private $entityManager;

    /** __construct
     *
     *  Constructor
     *
     * @param INotificationRepository $notificationRepository
     * @param IEmailGateway $emailGateway
     * @param EmailTemplate $emailTemplate
     * @param NotificationRenderer $notificationRenderer
     * @param EntityManager $entityManager
     */
    public function __construct(INotificationRepository $notificationRepository, IEmailGateway $emailGateway, EmailTemplate $emailTemplate, NotificationRenderer $notificationRenderer, EntityManager $entityManager) {
        $this->notificationRepository = $notificationRepository;
        $this->emailGateway = $emailGateway;
        $this->emailTemplate = $emailTemplate;
        $this->notificationRenderer = $notificationRenderer;
        $this->entityManager = $entityManager;
    }

    public function sendDailyNotifications() {
        $notifications = $this->notificationRepository->getDailyNotifications();
        $emailAddresses = [];
        /**
         * Group the notifications by email address as we will be sending all notifications (per email address) in one email.
         */
        array_walk($notifications, function (Notification $notification) use (&$emailAddresses) {
            $emailAddresses[$notification->getEmailAddress()][] = $notification;
            $this->entityManager->remove($notification);
        });
        /**
         * Loop through all the email addresses we have collected and send the notifications for each one.
         */
        array_walk($emailAddresses, function ($notifications, $emailAddress) {
            $notificationTypes = [];
            /**
             * Group all of this users notifications by their type. This makes it simple for us to show them grouped in the email.
             */
            array_walk($notifications, function (Notification $notification) use (&$notificationTypes) {
                $notificationTypes[$notification->getType()][] = $notification;
            });
            /**
             * Render a daily email for this user
             */
            $subject = "New notification" . (count($notifications) > 1 ? "s (" . count($notifications) . ")" : "") . " from CompEngine";
            $content = $this->renderDailyEmail($notificationTypes);
            $this->emailGateway->sendEmail($emailAddress, $subject, $content);
        });
        $this->entityManager->flush();
    }

    public function renderDailyEmail($notifications) {
        $templatePath = ROOT_PATH . "/private/templates/daily-notifications.html";
        $notificationRenderer = $this->notificationRenderer;
        return $this->emailTemplate->generateTemplate($templatePath, compact("notifications", "notificationRenderer"));
    }

    public function sendWeeklyNotifications() {
        $notifications = $this->notificationRepository->getWeeklyNotifications();
        $emailAddresses = [];
        /**
         * Group the notifications by email address as we will be sending all notifications (per email address) in one email.
         */
        array_walk($notifications, function (Notification $notification) use (&$emailAddresses) {
            $emailAddresses[$notification->getEmailAddress()][] = $notification;
            $this->entityManager->remove($notification);
        });
        /**
         * Loop through all the email addresses we have collected and send the notifications for each one.
         */
        array_walk($emailAddresses, function ($notifications, $emailAddress) {
            $notificationTypes = [];
            /**
             * Group all of this users notifications by their type. This makes it simple for us to show them grouped in the email.
             */
            array_walk($notifications, function (Notification $notification) use (&$notificationTypes) {
                $notificationTypes[$notification->getType()][] = $notification;
            });
            /**
             * Render a daily email for this user
             */
            $subject = "You received notifications this week on CompEngine";
            $content = $this->renderWeeklyEmail($notificationTypes, $notifications[0]->getName(), $notifications[0]->getUnsubscribeLink());
            $this->emailGateway->sendEmail($emailAddress, $subject, $content);
        });
        $this->entityManager->flush();
    }

    public function renderWeeklyEmail($notifications, $name, $unsubscribeLink) {
        $templatePath = ROOT_PATH . "/private/templates/weekly-notifications.html";
        $notificationRenderer = $this->notificationRenderer;
        return $this->emailTemplate->generateTemplate($templatePath, compact("name", "unsubscribeLink", "notifications", "notificationRenderer"));
    }
}
