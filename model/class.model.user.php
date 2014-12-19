<?php

class user {

    private $UID;
    private $Username;
    private $Password;
    private $Timestamp;

    function __construct($data) {
        $this->fromJson($data);
    }

    private function fromJson($data) {
        foreach ($data AS $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getUid() {
        return $this->UID;
    }

    public function setUid($uid) {
        $this->UID = $uid;
    }

    public function getUsername() {
        return $this->Username;
    }

    public function getPassword() {
        return $this->Password;
    }

    public function getTimestamp() {
        return $this->Timestamp;
    }

    public function toArray() {
        return get_object_vars($this);
    }
}

?>