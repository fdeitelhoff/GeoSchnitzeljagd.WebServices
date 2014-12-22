<?php

class Logging {

    private $db;

    private $server;
    private $request;
    private $body;

    function __construct($db, $server, $request, $body) {
        $this->db = $db;

        $this->server = $server;
        $this->request = $request;
        $this->body = $body;
    }

    public function log() {
		$route = trim($this->request['request']);
		$parameter = str_replace('/', '', trim($this->request['param']));
		$method = trim($this->server['REQUEST_METHOD']);
		$body = trim($this->body);
        $username = 'N/A';

        if (!empty($this->server['PHP_AUTH_USER'])) {
            $username = trim($this->server['PHP_AUTH_USER']);
        }

        $timestamp = date('Y-m-d H:m:s');

        $this->db->newQuery("INSERT INTO logging (Method, Route, Parameter, Body, User) VALUES (" .
            "'" . $this->db->escapeInput($method) . "', '" .
            $this->db->escapeInput($route) . "', '" .
            $this->db->escapeInput($parameter) . "', '" .
            $this->db->escapeInput($body). "', '" .
            $this->db->escapeInput($username) . "')");
    }
}

?>