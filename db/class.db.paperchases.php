<?php

class Paperchases {

    private $db;

    function __construct($db) {
        $this->db = $db;
    }

    public function all() {
        $this->db->newQuery("SELECT Paperchases.PID, UID, Name, Timestamp FROM Paperchases INNER JOIN Marks ON Paperchases.PID = Marks.PID");

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