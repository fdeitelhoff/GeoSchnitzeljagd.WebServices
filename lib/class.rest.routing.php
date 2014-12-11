<?php

class restRoute {

	private $db;
	private $routes;

	function __construct($db, $routes) {
		$this->db = $db;
		$this->routes = $routes;
	}

	public function route($route, $parameter, $method) {
		if (!array_key_exists($route, $routes)) {
			throw new Exception("Die Route '" + $route + "' gibt es nicht!");
		}
	
		switch ($route) {
			
		}
	}
}

?>