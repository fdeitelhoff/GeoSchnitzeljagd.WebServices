<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

function exception_handler($exception) {
  echo "<pre>Nicht aufgefangene Exception: " , print_r($exception), "</pre>";
}

set_exception_handler('exception_handler');

require_once(__DIR__ . '/config.php');

require_once(__DIR__ . '/../db/class.db.mysql.php');
require_once(__DIR__ . '/../db/class.db.users.php');

require_once(__DIR__ . '/../rest/class.rest.routing.php');

$db = new db($server, $user, $password, $database, true, true, true);

$users = new users($db);

?>