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
                return $this->createUser($body);
                break;
            case 'DELETE':
                break;
        }
    }

    private function getWithId($parameter) {
        $this->db->newQuery("SELECT UID, Username, Password, Timestamp FROM Users WHERE UID = " . $this->db->escapeInput($parameter));

        if ($this->db->getError()) {
            throw new Exception($this->db->getErrorMsg());
        }

        $data = array();

        while($result = $this->db->getObjectResults()) {
            $data[] = $result;
        }

        return json_encode($data);
    }

    private function createUser($body) {
        $user = new user(json_decode($body, true));

        if (!$this->userExists($user)) {
            $this->db->newQuery("INSERT INTO Users (Username, Password, Timestamp) VALUES ('" .
                $this->db->escapeInput($user->getUsername()) . "', '" .
                $this->db->escapeInput($user->getPassword()) . "', " .
                $this->db->escapeInput(strtotime($user->getTimestamp())) . ")");

            if ($this->db->getError()) {
                throw new Exception($this->db->getErrorMsg());
            }

            $user->setUid($this->db->getLastInsertID());

            throw new RestStatus(201, "The user was successfully created!", $user);
        }
        else
        {
            throw new RestStatus(409, "The user already exists!");
        }
    }

    private function userExists($user) {
        $this->db->newQuery("SELECT Count(*) FROM Users WHERE Username = '" . $this->db->escapeInput($user->getUsername()) . "'");

        if ($this->db->getError()) {
            throw new Exception($this->db->getErrorMsg());
        }

        return $this->db->getResult() == 0 ? false : true;
    }
}

?>