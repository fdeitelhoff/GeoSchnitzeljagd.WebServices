<?php

class Users {

    private $db;

    function __construct($db) {
        $this->db = $db;
    }

    public function all() {
        $this->db->newQuery("SELECT UID, Username, Password, Timestamp FROM users");

		if ($this->db->getError()) {
			throw new Exception($this->db->getErrorMsg());
		}

		$data = array();

		while($result = $this->db->getObjectResults()) {
			$data[] = $result;
		}

		return json_encode($data);
    }

    public function withData($parameter, $method, $body) {
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
            $status = new RestStatus(400, "Parameters are missing!");
            return $status->toJson();
        }

        $this->db->newQuery("SELECT UID, Username, Password, Timestamp FROM users WHERE UID = '" . $this->db->escapeInput($parameter) . "'");

        if ($this->db->getError()) {
            throw new Exception($this->db->getErrorMsg());
        }

        $data = array();

        while($result = $this->db->getObjectResults()) {
            $data[] = $result;
        }

        return json_encode($data);
    }

    public function register($body) {
        return $this->createUser($body);
    }

    private function createUser($body) {
        if (empty($body)) {
            $status = new RestStatus(400, "Content is missing!");
            return $status->toJson();
        }

        $user = new User(json_decode($body, true));

        if (!$this->userExistsWithName($user)) {
            $timestamp = date('Y-m-d H:m:s');

            $this->db->newQuery("INSERT INTO users (UID, Username, Password) VALUES (" .
                "'" . $this->db->escapeInput($user->getUid()) . "', '" .
                $this->db->escapeInput($user->getUsername()) . "', '" .
                $this->db->escapeInput($user->getPassword()) . "')");

            if ($this->db->getError()) {
                throw new Exception($this->db->getErrorMsg());
            }

            $user->setTimestamp($timestamp);

            $status = new RestStatus(201, "The user was successfully created!", $user);
            return $status->toJson();
        }
        else {
            $status = new RestStatus(409, "A user with this name already exists!");
            return $status->toJson();
        }
    }

    private function updateUser($body) {
        if (empty($body)) {
            $status = new RestStatus(400, "Content is missing!");
            return $status->toJson();
        }

        $user = new User(json_decode($body, true));

        if (!$this->userExistsWithId($user->getUid())) {
            $status = new RestStatus(404, "The user does not exists!");
            return $status->toJson();

        } else if (!$this->userExistsWithName($user)) {
            $timestamp = date('Y-m-d H:m:s');

            $this->db->newQuery("UPDATE users SET Username = '" . $this->db->escapeInput($user->getUsername()) .
                "', Timestamp = '" . $timestamp . "' WHERE UID = '" . $this->db->escapeInput($user->getUid()) . "'");

            if ($this->db->getError()) {
                throw new Exception($this->db->getErrorMsg());
            }

            if ($this->db->getAffectedRowCount() != 1) {
                $status = new RestStatus(500, "The user was not updated successfully!", $user);
                return $status->toJson();
            }

            $user->setTimestamp($timestamp);

            $status = new RestStatus(200, "The user was successfully updated!", $user);
            return $status->toJson();

        } else {
            $status = new RestStatus(409, "A user with this name already exists!");
            return $status->toJson();
        }
    }

    private function deleteUser($parameter) {
        if (empty($parameter)) {
            $status = new RestStatus(400, "Parameters are missing!");
            return $status->toJson();
        }

        if ($this->userExistsWithId($parameter)) {
            $this->db->newQuery("DELETE FROM users WHERE UID = '" . $this->db->escapeInput($parameter) . "'");

            if ($this->db->getError()) {
                throw new Exception($this->db->getErrorMsg());
            }

            $status = new RestStatus(200, "The user was successfully deleted!");
            return $status->toJson();
        }
        else
        {
            $status = new RestStatus(404, "The user does not exists!");
            return $status->toJson();
        }
    }

    private function userExistsWithName($user) {
        $this->db->newQuery("SELECT Count(*) FROM users WHERE Username = '" . $this->db->escapeInput($user->getUsername()) . "'");

        if ($this->db->getError()) {
            throw new Exception($this->db->getErrorMsg());
        }

        return $this->db->getResult() == 0 ? false : true;
    }

    private function userExistsWithId($id) {
        $this->db->newQuery("SELECT Count(*) FROM users WHERE UID = '" . $this->db->escapeInput($id) . "'");

        if ($this->db->getError()) {
            throw new Exception($this->db->getErrorMsg());
        }

        return $this->db->getResult() == 0 ? false : true;
    }
}

?>