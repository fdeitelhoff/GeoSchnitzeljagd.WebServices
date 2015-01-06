<?php

class PaperchaseCompleted {

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
}

?>