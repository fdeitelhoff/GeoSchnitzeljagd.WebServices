<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

function exception_handler($exception) {
  echo "<pre>Nicht aufgefangene Exception: " , print_r($exception), "</pre>";
}

set_exception_handler('exception_handler');

require_once(__DIR__ . '/../conf/config.php');

require_once(__DIR__ . '/../db/class.db.mysql.php');

require_once(__DIR__ . '/../log/class.db.logging.php');

$db = new Db($server, $user, $password, $database, false, false, false);

?>