<?php

class PaperchaseCompleted {

    private $PSID;
    private $PID;
    private $UID;
    private $StartTime;
    private $EndTime;

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

    public function setPsid($psid) {
        $this->PSID = $psid;
    }

    public function getPid() {
        return $this->PID;
    }

    public function getUid() {
        return $this->UID;
    }

    public function getStartTime() {
        return $this->StartTime;
    }

    public function getEndTime() {
        return $this->EndTime;
    }

    public function toArray() {
        return get_object_vars($this);
    }
}

?>