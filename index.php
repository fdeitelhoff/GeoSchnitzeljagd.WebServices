<?php

try {
    require_once(__DIR__ . '/conf/global.php');

    $routing = new restRoute($db, ["user", "users"],
                             $users);

    echo $routing->route($_REQUEST['request'],
                         $_REQUEST['param'],
                         $_SERVER['REQUEST_METHOD'],
                         file_get_contents('php://input'));

} catch (RestStatus $status) {
    echo $status->toJson();
}
?>