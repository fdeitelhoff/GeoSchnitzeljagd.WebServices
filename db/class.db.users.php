<?php

class users {

    private $db;

    function __construct($db) {
        $this->db = $db;
    }

    public function all() {
        $this->db->newQuery("SELECT UID, Username, Password, Timestamp FROM Users");

		if ($this->db->getError()) {
			throw new Exception($this->db->getErrorMsg());
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
                return $this->updateUser($body);
                break;
            case 'POST':
                return $this->createUser($body);
                break;
            case 'DELETE':
                return $this->deleteUser($parameter);
                break;
        }
    }

    private function getWithId($parameter) {
        if (empty($parameter)) {
            throw new RestStatus(400, "Parameters are missing!");
        }

        $this->db->newQuery("SELECT UID, Username, Password, Timestamp FROM Users WHERE UID = '" . $this->db->escapeInput($parameter) . "'");

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
        if (empty($body)) {
            throw new RestStatus(400, "Content is missing!");
        }

        $user = new user(json_decode($body, true));

        if (!$this->userExistsWithName($user)) {
            $uuid = GUID();

            $this->db->newQuery("INSERT INTO Users (UID, Username, Password) VALUES (" .
                "'" . $uuid . "', '" .
                $this->db->escapeInput($user->getUsername()) . "', '" .
                $this->db->escapeInput($user->getPassword()) . "')");

            if ($this->db->getError()) {
                throw new Exception($this->db->getErrorMsg());
            }

            $user->setUid($uuid);

            throw new RestStatus(201, "The user was successfully created!", $user);
        }
        else
        {
            throw new RestStatus(409, "A user with this name already exists!");
        }
    }

    private function updateUser($body) {
        if (empty($body)) {
            throw new RestStatus(400, "Content is missing!");
        }

        $user = new user(json_decode($body, true));

        if (!$this->userExistsWithId($user->getUid())) {
            throw new RestStatus(404, "The user does not exists!");
        } else if (!$this->userExistsWithName($user)) {
            $this->db->newQuery("UPDATE Users SET Username = '" . $this->db->escapeInput($user->getUsername()) .
                "', Password = '" . $this->db->escapeInput($user->getPassword()) .
                "', Timestamp = NOW() WHERE UID = '" . $this->db->escapeInput($user->getUid()) . "'");

            if ($this->db->getError()) {
                throw new Exception($this->db->getErrorMsg());
            }

            if ($this->db->getAffectedRowCount() != 1) {
                throw new RestStatus(500, "The user was not updated successfully!", $user);
            }

            throw new RestStatus(200, "The user was successfully updated!", $user);
        }
        else {
            throw new RestStatus(409, "A user with this name already exists!");
        }
    }

    private function deleteUser($parameter) {
        if (empty($parameter)) {
            throw new RestStatus(400, "Parameters are missing!");
        }

        if ($this->userExistsWithId($parameter)) {
            $this->db->newQuery("DELETE FROM Users WHERE UID = '" . $this->db->escapeInput($parameter) . "'");

            if ($this->db->getError()) {
                throw new Exception($this->db->getErrorMsg());
            }

            throw new RestStatus(200, "The user was successfully deleted!");
        }
        else
        {
            throw new RestStatus(404, "The user does not exists!");
        }
    }

    private function userExistsWithName($user) {
        $this->db->newQuery("SELECT Count(*) FROM Users WHERE Username = '" . $this->db->escapeInput($user->getUsername()) . "'");

        if ($this->db->getError()) {
            throw new Exception($this->db->getErrorMsg());
        }

        return $this->db->getResult() == 0 ? false : true;
    }

    private function userExistsWithId($id) {
        $this->db->newQuery("SELECT Count(*) FROM Users WHERE UID = '" . $this->db->escapeInput($id) . "'");

        if ($this->db->getError()) {
            throw new Exception($this->db->getErrorMsg());
        }

        return $this->db->getResult() == 0 ? false : true;
    }
}

?>