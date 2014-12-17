<?php

class restRoute {

	private $db;
	private $routes;

	private $users;

	function __construct($db, $routes, $users) {
		$this->db = $db;
		$this->routes = $routes;

		$this->users = $users;
	}

	public function route($route, $parameter, $method, $body) {
		$parameter = str_replace('/', '', $parameter);

		if (!in_array($route, $this->routes)) {
			throw new Exception("Die Route '" + $route + "' gibt es nicht!");
		}
	
		switch ($route) {
			case 'user':
				return $this->users->withId($parameter, $method, $body);
				break;
			case 'users':
				return $this->users->all();
				break;
		}
	}
}

?>