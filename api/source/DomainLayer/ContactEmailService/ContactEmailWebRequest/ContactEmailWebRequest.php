<?php

namespace DomainLayer\ContactEmailService\ContactEmailWebRequest;
use PresentationLayer\Routes\EInvalidInputs;

/**
 * Class ContactEmailWebRequest
 * @package DomainLayer\ContactEmailService\ContactEmailWebRequest
 */
class ContactEmailWebRequest {

    private $payload;

    public function injectPayload($payload) {
        $this->payload = $payload;
    }

    public function getName() {
        if (empty($this->payload["name"])) {
            throw new EInvalidInputs("A name is required.");
        }
        return $this->payload["name"];
    }

    public function getEmailAddress() {
        if (empty($this->payload["emailAddress"])) {
            throw new EInvalidInputs("An email address is required.");
        }
        if (!filter_var($this->payload["emailAddress"], FILTER_VALIDATE_EMAIL)) {
            throw new EInvalidInputs("The email address is invalid.");
        }
        return $this->payload["emailAddress"];
    }

    public function getMessage() {
        if (empty($this->payload["message"])) {
            throw new EInvalidInputs("A message is required.");
        }
        return $this->payload["message"];
    }

    public function getSendCopy() {
        if (!isset($this->payload["sendCopy"])) {
            throw new EInvalidInputs("A sendCopy boolean field is required.");
        }
        if (!is_bool($this->payload["sendCopy"])) {
            throw new EInvalidInputs("The sendCopy field must be a boolean value.");
        }
        return $this->payload["sendCopy"];
    }

}