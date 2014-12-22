<?php

class Auth {

    private $db;

    private $username;
    private $password;

    function __construct($db, $server) {
        $this->db = $db;

        if (!empty($server['PHP_AUTH_USER'])) {
            $this->username = trim($server['PHP_AUTH_USER']);
        }

        if (!empty($_SERVER['PHP_AUTH_PW'])) {
            $this->password = trim($_SERVER['PHP_AUTH_PW']);
        }
    }

    public function authorize() {
        if (empty($this->username) || empty($this->password)) {
            return false;
        }

        $this->db->newQuery("SELECT Count(*) FROM users WHERE Username = '" .
                            $this->db->escapeInput($this->username) . "' AND Password = '" .
                            $this->db->escapeInput($this->password) . "'");

        if ($this->db->getError()) {
            throw new Exception($this->db->getErrorMsg());
        }

        return $this->db->getResult() == 0 ? false : true;
    }
}

?>