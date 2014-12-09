<?php

/*print_r($_REQUEST);

print_r($_SERVER);*/

echo "Request method:\n";
echo $_SERVER['REQUEST_METHOD'] . "\n";

echo "Request data: \n";
print_r($_REQUEST);

echo "User: " . $_SERVER['PHP_AUTH_USER'] . "\n";
echo "PWD: " . $_SERVER['PHP_AUTH_PW'] . "\n";

echo "Request Body: \n";
echo file_get_contents('php://input');

?>