<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

function exception_handler($exception) {
  echo "<pre>Nicht aufgefangene Exception: " , print_r($exception), "</pre>";
}

set_exception_handler('exception_handler');

require_once(__DIR__ . '/config.php');

require_once(__DIR__ . '/../common/functions.common.php');

require_once(__DIR__ . '/../auth/class.auth.php');

require_once(__DIR__ . '/../db/class.db.mysql.php');
require_once(__DIR__ . '/../log/class.db.logging.php');
require_once(__DIR__ . '/../db/class.db.users.php');
require_once(__DIR__ . '/../db/class.db.paperchases.php');

require_once(__DIR__ . '/../model/class.model.user.php');
require_once(__DIR__ . '/../model/class.model.paperchase.php');
require_once(__DIR__ . '/../model/class.model.mark.php');

require_once(__DIR__ . '/../rest/class.rest.routing.php');
require_once(__DIR__ . '/../rest/class.rest.status.php');

?>