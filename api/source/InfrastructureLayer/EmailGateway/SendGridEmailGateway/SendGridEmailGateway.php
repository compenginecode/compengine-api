<?php

namespace InfrastructureLayer\EmailGateway\SendGridEmailGateway;

use ConfigurationLayer\ApplicationConfig\ApplicationConfig;
use ConfigurationLayer\ApplicationConfigFactory\ApplicationConfigFactory;
use InfrastructureLayer\EmailTemplate\EmailTemplate;
use InfrastructureLayer\EmailGateway\IEmailGateway;
use SendGrid\Email;

/**
 * Class SendGridEmailGateway
 * @package InfrastructureLayer\EmailGateway\SendGridEmailGateway
 */
class SendGridEmailGateway implements IEmailGateway
{
    /** client
     *
     *
     *
     * @var \SendGrid
     */
    private $client;

    /** from
     *
     *
     *
     * @var string
     */
    private $from;

    /** emailTemplate
     *
     *
     *
     * @var EmailTemplate
     */
    private $emailTemplate;

    /** sendEmail
     *
     *
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     */
    public function sendEmail($to, $subject, $content) {
        $email = new Email();
        $email
            ->addTo($to)
            ->setFrom($this->from)
            ->setSubject($subject)
            ->setHtml($content)
        ;
        $this->client->send($email);
    }

    /** __construct
     *
     *  Constructor
     *
     * @param EmailTemplate $emailTemplate
     */
    public function __construct(EmailTemplate $emailTemplate, ApplicationConfig $applicationConfig) {
        $applicationConfig = ApplicationConfigFactory::createFromFile(ROOT_PATH . "/private/configuration/configuration.ini", "testing");
        $this->from = $applicationConfig->get("email_from");
        $this->client = new \SendGrid($applicationConfig->get("sendgrid_api_key"));
        $this->emailTemplate = $emailTemplate;
    }
}
