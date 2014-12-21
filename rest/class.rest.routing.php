<?php

class RestRoute {

	private $db;

	private $server;
	private $request;

	private $routes;

	private $auth;

	private $users;
	private $paperchases;

	function __construct($db, $server, $request, $routes, $users, $paperchases) {
		$this->db = $db;

		$this->server = $server;
		$this->request = $request;

		$this->routes = $routes;

		$this->auth = new Auth($db, $server);

		$this->users = $users;
		$this->paperchases = $paperchases;
	}

	public function route($body) {
		$route = trim($this->request['request']);
		$parameter = trim($this->request['param']);
		$method = trim($this->server['REQUEST_METHOD']);
		$body = trim($body);

		// If someone wants to register, there's no need te be authorized.
		if ($route == 'register') {
			return $this->users->register($body);
		} else if (!$this->auth->authorize()) {
			$status = new RestStatus(401, "You're not authorized!");
			return $status->toJson();
		}

		$parameter = str_replace('/', '', $parameter);

		if (!in_array($route, $this->routes)) {
			throw new Exception("Die Route '" + $route + "' gibt es nicht!");
		}
	
		switch ($route) {
			case 'users':
				return $this->users->all();
				break;
			case 'user':
				return $this->users->withId($parameter, $method, $body);
				break;
			case 'paperchases':
				return $this->paperchases->all();
				break;
			default:

		}
	}
}

?>