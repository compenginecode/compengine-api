<?php

namespace InfrastructureLayer\EmailGateway;

/**
 * Interface IEmailGateway
 * @package InfrastructureLayer\EmailGateway
 */
interface IEmailGateway
{
    /** sendEmail
     *
     *
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     */
    public function sendEmail($to, $subject, $content);
}
