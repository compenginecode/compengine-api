<?php

namespace DomainLayer\ContactEmailService;

use SendGrid\Content;
use SendGrid\Email;
use SendGrid\Mail;

/**
 * Class ContactEmailService
 * @package DomainLayer\ContactEmailService
 */
class ContactEmailService {

    public function createMail($name, $recipient, $message, $subject) {
        global $configuration;

        $body = '
            <html>
                <body>
                    <p>Name: [name]</p>
                    <p>Email: [mail]</p>
                    <p>Comments: [comments]</p>
                </body>
            </html>
        ';
        $replacements = array(
            "[name]" => $name,
            "[mail]" => $recipient,
            "[comments]" => nl2br($message)
        );
        $body = str_replace(array_keys($replacements), array_values($replacements), $body);

        $mail = new Email();
        $mail
            ->addTo($recipient)
            ->setFrom($configuration->get("email_from"))
            ->setSubject($subject)
            ->setHtml($body)
        ;

        return $mail;
    }

    public function sendEmails($name, $recipient, $message, $sendCopy) {
        global $configuration;

        $mail = $this->createMail($name, $configuration->get("default_sender_email_address"), $message, "You have received a new message from the Comp-Engine Contact page");
        $sg = new \SendGrid($configuration->get("sendgrid_api_key"));
        $sg->send($mail);

        if ($sendCopy) {
            $mail = $this->createMail($name, $recipient, $message, "You emailed Comp-Engine");
            $sg->send($mail);
        }
    }

}