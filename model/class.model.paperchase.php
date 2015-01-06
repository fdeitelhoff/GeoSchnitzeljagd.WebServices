<?php

class Paperchase {

    private $PID;
    private $UID;
    private $Name;
    private $Timestamp;

    private $Marks;

    function __construct($data) {
        $this->fromJson($data);
    }

    private function fromJson($data) {
        foreach ($data AS $key => $value) {
            if(property_exists(__CLASS__, $key)) {
                $this->{$key} = $value;
            }
        }

        $this->Marks = array(); //new Mark($data);
    }

    public function getPid() {
        return $this->PID;
    }

    public function setPid($pid) {
        $this->PID = $pid;
    }

    public function getUid() {
        return $this->UID;
    }

    public function setUid($uid) {
        $this->UID = $uid;
    }

    public function getName() {
        return $this->Name;
    }

    public function setName($name) {
        $this->Name = $name;
    }

    public function getTimestamp() {
        return $this->Timestamp;
    }

    public function setTimestamp($timestamp) {
        $this->Timestamp = $timestamp;
    }

    public function getMarks() {
        return $this->Marks;
    }

    public function addMark($data) {
        $this->Marks[] = new Mark($data);
    }

    public function toArray() {
        $data = get_object_vars($this);

        // The method above creates two empty array elements within the mark key. We remove them first.
        $data['Marks'] = array();

        foreach ($this->Marks AS $key => $value) {
            $data['Marks'][] = $value->toArray();
        }

        return $data;
    }
}

?>