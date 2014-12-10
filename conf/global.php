<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('config.php');
require_once('lib/class.db.mysql.php');

$db = new db($server, $user, $password, $database, true, true, true);

echo $db;

?>