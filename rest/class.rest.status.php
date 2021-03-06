<?php

class RestStatus {

    private $statusCode;
    private $response;
    private $data;

    function __construct($statusCode, $response = "", $data = "") {
        $this->statusCode = $statusCode;
        $this->response = $response;
        $this->data = $data;
    }

    public function getStatusCode() {
        return $this->statusCode;
    }

    public function getResponse() {
        return $this->response;
    }

    public function getData() {
        return $this->data;
    }

    public function toJson() {
        $json = array('status' => $this->getStatusCode(),
                      'message' => $this->getResponse(),
                      'data' => empty($this->getData()) ? "" : $this->getData()->toArray());

        // Status messages are sent in a pretty format so they're more human readable.
        return json_encode($json, JSON_PRETTY_PRINT);
    }
}

?>