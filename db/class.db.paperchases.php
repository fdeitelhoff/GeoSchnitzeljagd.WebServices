<?php

class Paperchases {

    private $db;

    function __construct($db) {
        $this->db = $db;
    }

    public function all() {
        $this->db->newQuery("SELECT paperchases.PID, UID, Name, paperchases.Timestamp, MID, Hint, Latitude, "
            . "Longitude, Sequence FROM paperchases INNER JOIN marks ON paperchases.PID = marks.PID ORDER BY Sequence");

        if ($this->db->getError()) {
            throw new Exception($this->db->getErrorMsg());
        }

        $paperchases = array();

        while($result = $this->db->getObjectResults()) {
            $paperchase = new Paperchase($result);

            if (!array_key_exists($paperchase->getPid(), $paperchases)) {
                $paperchases[$paperchase->getPid()] = $paperchase;
            } else {
                $paperchases[$paperchase->getPid()]->addMark($result);
            }
        }

        $data = array();
        foreach ($paperchases AS $key => $value) {
            $data[] = $value->toArray();
        }

        return json_encode($data);
    }
}

?>