<?php

require_once('conf/global.php');

echo "Request method:\n";
echo $_SERVER['REQUEST_METHOD'] . "\n";

echo "Request data: \n";
print_r($_REQUEST);

echo "User: " . $_SERVER['PHP_AUTH_USER'] . "\n";
echo "PWD: " . $_SERVER['PHP_AUTH_PW'] . "\n";

echo "Request Body: \n";
echo file_get_contents('php://input');

/*

	public function GetPageData($pagenames, $language) {
		$pages = implode("', '", split(",", $pagenames));

		$this->db->newQuery("SELECT
								Pagename,
								Variable,
								Content
							 FROM
							 	Multilanguage
							 WHERE
							 	Language = '" . $language . "'
							 AND
							 	Pagename IN ('" . $pages ."')");

		if ($this->db->getError()) {
			echo $this->db->getErrorMsg();
			exit();
		}

		$data = array();

		while($result = $this->db->getObjectResults()) {
			$data[] = $result; //array("Pagename" => $result->Pagename, "Variable" => $result->Variable, "Content" => html_entity_decode($result->Content));
		}

		return json_encode($data);
	}

	*/
?>