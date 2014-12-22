<?php

class Paperchases {

    private $db;

    function __construct($db) {
        $this->db = $db;
    }

    public function all() {
        $this->db->newQuery("SELECT paperchases.PID, UID, Name, Timestamp FROM paperchases INNER JOIN marks ON paperchases.PID = marks.PID");

        if ($this->db->getError()) {
            throw new Exception($this->db->getErrorMsg());
        }

        $data = array();

        while($result = $this->db->getObjectResults()) {
            $data[] = $result;
        }

        return json_encode($data);
    }
}

?>