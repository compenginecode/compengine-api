<?php

namespace DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService\TimeSeriesDownloadWebRequest;

use PresentationLayer\Routes\EInvalidInputs;

/**
 * Class TimeSeriesDownloadWebRequest
 * @package DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService\TimeSeriesDownloadWebRequest
 */
class TimeSeriesDownloadWebRequest {

    private $payload;

    public function injectPayload($payload) {
        $this->payload = $payload;
    }

    public function getExportType() {
        if (empty($this->payload["exportType"])) {
            throw new EInvalidInputs("An exportType is required.");
        }
        if ($this->payload["exportType"] !== "json" && $this->payload["exportType"] !== "csv") {
            throw new EInvalidInputs("The export type must be either json or csv.");
        }
        return $this->payload["exportType"];
    }

    public function getEmailAddress() {
        if (empty($this->payload["emailAddress"])) {
            throw new EInvalidInputs("An emailAddress is required.");
        }
        if (!filter_var($this->payload["emailAddress"], FILTER_VALIDATE_EMAIL)) {
            throw new EInvalidInputs("The email address is invalid.");
        }
        return $this->payload["emailAddress"];
    }

}