<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/../lib/class.db.mysql.php');

require_once(__DIR__ . '/../lib/class.rest.routing.php');

$db = new db($server, $user, $password, $database, true, true, true);

?>