<?php

class restRoute {

	private $db;
	private $routes;

	private $users;
	private $paperchases;

	function __construct($db, $routes, $users, $paperchases) {
		$this->db = $db;
		$this->routes = $routes;

		$this->users = $users;
		$this->paperchases = $paperchases;
	}

	public function route($route, $parameter, $method, $body) {
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