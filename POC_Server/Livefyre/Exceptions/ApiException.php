<?php

namespace Livefyre\Exceptions;

/**
 * @codeCoverageIgnore
 */
class ApiException extends LivefyreException {

    private $apiStatus = array(
        "400" => "Please check the contents of your request. Error code 400.",
        "401" => "The request requires authentication via an HTTP Authorization header. Error code 401.",
        "403" => "The server understood the request; but is refusing to fulfill it. Error code 403.",
        "404" => "The requested resource was not found. Error code 404.",
        "500" => "Livefyre appears to be down. Please see status.livefyre.com or contact us for more information. Error code 500.",
        "501" => "The requested functionality is not currently supported. Error code 501.",
        "502" => "The server; while acting as a gateway or proxy; received an invalid response from the upstream server it accessed in attempting to fulfill the request at this time. Error code 502.",
        "503" => "The service is undergoing scheduled maintenance; and will be available again shortly. Error code 503."
    );

    public function __construct($statusCode) {
        parent::__construct($this->apiStatus[$statusCode]);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
