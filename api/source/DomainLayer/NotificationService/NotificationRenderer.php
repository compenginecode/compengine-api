<?php

namespace DomainLayer\NotificationService;

use DomainLayer\ORM\Notification\Notification;
use InfrastructureLayer\EmailTemplate\EmailTemplate;

/**
 * Class NotificationRenderer
 * @package DomainLayer\NotificationService
 */
class NotificationRenderer
{
    /** emailTemplate
     *
     *
     *
     * @var EmailTemplate
     */
    private $emailTemplate;

    /** __construct
     *
     *  Constructor
     *
     * @param EmailTemplate $emailTemplate
     */
    public function __construct(EmailTemplate $emailTemplate) {
        $this->emailTemplate = $emailTemplate;
    }

    /** render
     *
     *
     *
     * @param Notification $notification
     * @return string
     */
    public function render(Notification $notification) {
        $notificationKey = str_replace(" ", "-", strtolower($notification->getType()));
        $templatePath = ROOT_PATH . "/private/templates/notifications/{$notificationKey}.html";
        return $this->emailTemplate->generateTemplate($templatePath, compact("notification"));
    }
}
