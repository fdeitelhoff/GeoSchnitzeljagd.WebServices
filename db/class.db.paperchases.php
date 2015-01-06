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
                $paperchases[$paperchase->getPid()]->addMark($result);
            } else {
                $paperchases[$paperchase->getPid()]->addMark($result);
            }
        }

        $data = array();
        foreach ($paperchases AS $key => $value) {
            $data[] = $value->toArray();
        }

        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public function withData($parameter, $method, $body) {
        switch ($method) {
            /*case 'GET':
                return $this->getWithId($parameter);
                break;*/
            case 'PUT':
                return $this->updateUser($body);
                break;
            case 'POST':
                return $this->createPaperchase($body);
                break;
            case 'DELETE':
                return $this->deleteUser($parameter);
                break;
        }
    }

    private function createPaperchase($body) {
        if (empty($body)) {
            $status = new RestStatus(400, "Content is missing!");
            return $status->toJson();
        }

        $data = json_decode($body, true);

        $paperchase = new Paperchase($data);
        foreach ($data['Marks'] AS $key => $value) {
            $paperchase->addMark($value);
        }

        if (!$this->paperchaseExistsWithName($paperchase)) {
            $timestamp = date('Y-m-d H:m:s');

            $this->db->newQuery("INSERT INTO paperchases (PID, UID, Name, Timestamp) VALUES (" .
                "'" . $this->db->escapeInput($paperchase->getPid()) . "', '" .
                $this->db->escapeInput($paperchase->getUid()) . "', '" .
                $this->db->escapeInput($paperchase->getName()) . "', '" .
                $this->db->escapeInput($timestamp) . "')");

            if ($this->db->getError()) {
                throw new Exception($this->db->getErrorMsg());
            }

            $paperchase->setTimestamp($timestamp);

            foreach ($paperchase->getMarks() AS $key => $value) {
                $this->db->newQuery("INSERT INTO marks (MID, PID, Hint, Latitude, Longitude, Sequence) VALUES (" .
                    "'" . $this->db->escapeInput($value->getMid()) . "', '" .
                    $this->db->escapeInput($value->getPid()) . "', '" .
                    $this->db->escapeInput($value->getHint()) . "', '" .
                    $this->db->escapeInput($value->getLatitude()) . "', '" .
                    $this->db->escapeInput($value->getLongitude()) . "', '" .
                    $this->db->escapeInput($value->getSequence()) . "')");

                if ($this->db->getError()) {
                    throw new Exception($this->db->getErrorMsg());
                }
            }

            $status = new RestStatus(201, "The paperchase was successfully created!", $paperchase);
            return $status->toJson();
        }
        else {
            $status = new RestStatus(409, "A paperchase with this name already exists!");
            return $status->toJson();
        }
    }

    private function paperchaseExistsWithName($paperchase) {
        $this->db->newQuery("SELECT Count(*) FROM paperchases WHERE Name = '" . $this->db->escapeInput($paperchase->getName()) . "'");

        if ($this->db->getError()) {
            throw new Exception($this->db->getErrorMsg());
        }

        return $this->db->getResult() == 0 ? false : true;
    }
}

?>