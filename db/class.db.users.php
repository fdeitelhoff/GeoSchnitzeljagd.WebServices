<?php

class users {

    private $db;

    function __construct($db) {
        $this->db = $db;
    }

    public function all() {
        $this->db->newQuery("SELECT UID, Username, Password, Timestamp FROM Users");

		if ($this->db->getError()) {
			throw new Exception($this->db->getError());
		}

		$data = array();

		while($result = $this->db->getObjectResults()) {
			$data[] = $result;
		}

		return json_encode($data);
    }

    public function withId($parameter, $method, $body) {
        switch ($method) {
            case 'GET':
                return $this->getWithId($parameter);
                break;
            case 'PUT':
                break;
            case 'POST':
                return $this->createUser($parameter, $body);
                break;
            case 'DELETE':
                break;
        }
    }

    private function getWithId($parameter) {
        $this->db->newQuery("SELECT UID, Username, Password, Timestamp FROM Users WHERE UID = " . $this->db->escapeInput($parameter));

        if ($this->db->getError()) {
            throw new Exception($this->db->getError());
        }

        $data = array();

        while($result = $this->db->getObjectResults()) {
            $data[] = $result;
        }

        return json_encode($data);
    }

    private function createUser($parameter, $body) {
        $this->db->newQuery("INS");

        return "OK " . $body;
    }
}

?>