<?php

class Mark {

    private $MID;
    private $PID;
    private $Latitude;
    private $Longitude;
    private $Hint;
    private $Sequence;

    function __construct($data) {
        $this->fromJson($data);
    }

    private function fromJson($data) {
        foreach ($data AS $key => $value) {
            if(property_exists(__CLASS__, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function getMid() {
        return $this->MID;
    }

    public function getPid() {
        return $this->PID;
    }

    public function getLatitude() {
        return $this->Latitude;
    }

    public function getLongitude() {
        return $this->Longitude;
    }

    public function getHint() {
        return $this->Hint;
    }

    public function getSequence() {
        return $this->Sequence;
    }

    public function toArray() {
        return get_object_vars($this);
    }
}

?>