<?php

require_once(__DIR__ . '/conf/global.php');

$db = new Db($server, $user, $password, $database, false, false, false);

// In some PHP versions this input stream can only be read once.
// So we read it just once here!
$body = file_get_contents('php://input');

$logging = new Logging($db,
                       $_SERVER,
                       $_REQUEST,
                       $body);

$logging->log();

$users = new Users($db);
$paperchases = new Paperchases($db);

$routing = new RestRoute($db,
                         $_SERVER,
                         $_REQUEST,
                         $users,
                         $paperchases);

echo $routing->route($body);

?>