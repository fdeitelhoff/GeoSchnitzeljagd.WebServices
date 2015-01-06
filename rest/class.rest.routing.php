<?php

class RestRoute {

	private $db;

	private $server;
	private $request;

	private $auth;

	private $users;
	private $paperchases;

	function __construct($db, $server, $request, $users, $paperchases) {
		$this->db = $db;

		$this->server = $server;
		$this->request = $request;

		$this->auth = new Auth($db, $server);

		$this->users = $users;
		$this->paperchases = $paperchases;
	}

	public function route($body) {
		$route = trim($this->request['request']);
		$parameter = str_replace('/', '', trim($this->request['param']));
		$method = trim($this->server['REQUEST_METHOD']);
		$body = trim($body);

		// If someone wants to register, get all users or just uses an echo, there's no need te be authorized.
		switch ($route) {
			case 'register':
				return $this->users->register($body);
				break;
			case 'users':
				return $this->users->all();
				break;
			case 'echo-body':
				return $this->echoBody($body);
				break;
		}

		// Check if there is a valid username and password combination.
		if (!$this->auth->authorize()) {
			$status = new RestStatus(401, "You're not authorized!");
			return $status->toJson();
		}

		// This API endpoints are only accessible if the client is authorized.
		switch ($route) {
			case 'user':
				return $this->users->withData($parameter, $method, $body);
				break;
			case 'paperchases':
				return $this->paperchases->all();
				break;
			case 'paperchase':
				return $this->paperchases->withData($parameter, $method, $body);
				break;
			case 'paperchase-completed':
				return $this->paperchases->completedWithData($parameter, $method, $body);
				break;
			default:
				$status = new RestStatus(404, "API endpoint not found!");
				return $status->toJson();
				break;
		}
	}

	private function echoBody($body) {
		return $body;
	}
}

?>