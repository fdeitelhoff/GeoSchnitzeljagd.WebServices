<?php

require_once(__DIR__ . '/conf/global.php');

$db = new db($server, $user, $password, $database, false, false, false);

$logging = new Logging($db,
                       $_SERVER,
                       $_REQUEST,
                       file_get_contents('php://input'));

$logging->log();

$users = new users($db);
$paperchases = new Paperchases($db);

$routing = new restRoute($db,
                         $_SERVER,
                         $_REQUEST,
                         ['users', 'user', 'paperchases'],
                         $users,
                         $paperchases);

echo $routing->route(file_get_contents('php://input'));

?>