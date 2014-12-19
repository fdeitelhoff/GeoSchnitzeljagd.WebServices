<?php

require_once(__DIR__ . '/conf/global.php');

$auth = new Auth($db, $_SERVER);

if (!$auth->authorize()) {
    $status = new RestStatus(401, "You're not authorized!");
    echo $status->toJson();
    exit;
}

$routing = new restRoute($db,
                         ["user", "users"],
                         $users);

echo $routing->route($_REQUEST['request'],
                     $_REQUEST['param'],
                     $_SERVER['REQUEST_METHOD'],
                     file_get_contents('php://input'));
?>