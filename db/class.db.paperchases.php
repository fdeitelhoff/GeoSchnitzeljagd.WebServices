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
            case 'PUT':
                return $this->updatePaperchase($body);
                break;
            case 'POST':
                return $this->createPaperchase($body);
                break;
            case 'DELETE':
                return $this->deletePaperchase($parameter);
                break;
        }
    }

    public function completedWithData($parameter, $method, $body) {
        switch ($method) {
            case 'POST':
                return $this->paperchaseCompleted($body);
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

    private function updatePaperchase($body) {
        if (empty($body)) {
            $status = new RestStatus(400, "Content is missing!");
            return $status->toJson();
        }

        $data = json_decode($body, true);

        $paperchase = new Paperchase($data);
        foreach ($data['Marks'] AS $key => $value) {
            $paperchase->addMark($value);
        }

        if (!$this->paperchaseExistsWithId($paperchase->getPid())) {
            $status = new RestStatus(404, "The paperchase does not exists!");
            return $status->toJson();

        } else if (!$this->paperchaseExistsWithName($paperchase)) {
            $timestamp = date('Y-m-d H:m:s');

            $this->db->newQuery("UPDATE paperchases SET Name = '" . $this->db->escapeInput($paperchase->getName()) .
                "', Timestamp = '" . $timestamp . "' WHERE PID = '" . $this->db->escapeInput($paperchase->getPid()) . "'");

            if ($this->db->getError()) {
                throw new Exception($this->db->getErrorMsg());
            }

            if ($this->db->getAffectedRowCount() != 1) {
                $status = new RestStatus(500, "The paperchase was not updated successfully!", $user);
                return $status->toJson();
            }

            $paperchase->setTimestamp($timestamp);

            $this->db->newQuery("DELETE FROM marks WHERE PID = '" . $this->db->escapeInput($paperchase->getPid()) . "'");

            if ($this->db->getError()) {
                throw new Exception($this->db->getErrorMsg());
            }

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

            $status = new RestStatus(200, "The paperchase was successfully updated!", $paperchase);
            return $status->toJson();

        } else {
            $status = new RestStatus(409, "A paperchase with this name already exists!");
            return $status->toJson();
        }
    }

    private function deletePaperchase($parameter) {
        if (empty($parameter)) {
            $status = new RestStatus(400, "Parameters are missing!");
            return $status->toJson();
        }

        if ($this->paperchaseExistsWithId($parameter)) {
            $this->db->newQuery("DELETE FROM paperchases WHERE PID = '" . $this->db->escapeInput($parameter) . "'");

            if ($this->db->getError()) {
                throw new Exception($this->db->getErrorMsg());
            }

            $this->db->newQuery("DELETE FROM marks WHERE PID = '" . $this->db->escapeInput($parameter) . "'");

            if ($this->db->getError()) {
                throw new Exception($this->db->getErrorMsg());
            }

            $status = new RestStatus(200, "The paperchase was successfully deleted!");
            return $status->toJson();
        }
        else
        {
            $status = new RestStatus(404, "The paperchase does not exists!");
            return $status->toJson();
        }
    }

    private function paperchaseCompleted($body) {
        if (empty($body)) {
            $status = new RestStatus(400, "Content is missing!");
            return $status->toJson();
        }

        $paperchaseCompleted = new PaperchaseCompleted(json_decode($body, true));

        if ($this->paperchaseExistsWithId($paperchaseCompleted->getPid())) {
            $id = GUID();

            $this->db->newQuery("INSERT INTO paperchasecompleted (PSID, PID, UID, StartTime, EndTime) VALUES (" .
                "'" . $this->db->escapeInput($id) . "', '" .
                $this->db->escapeInput($paperchaseCompleted->getPid()) . "', '" .
                $this->db->escapeInput($paperchaseCompleted->getUid()) . "', '" .
                $this->db->escapeInput($paperchaseCompleted->getStartTime()) . "', '" .
                $this->db->escapeInput($paperchaseCompleted->getEndTime()) . "')");

            if ($this->db->getError()) {
                throw new Exception($this->db->getErrorMsg());
            }

            $paperchaseCompleted->setPsid($id);

            $status = new RestStatus(200, "The paperchase was successfully completed!", $paperchaseCompleted);
            return $status->toJson();
        } else {
             $status = new RestStatus(404, "The paperchase does not exists!");
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

    private function paperchaseExistsWithId($id) {
        $this->db->newQuery("SELECT Count(*) FROM paperchases WHERE PID = '" . $this->db->escapeInput($id) . "'");

        if ($this->db->getError()) {
            throw new Exception($this->db->getErrorMsg());
        }

        return $this->db->getResult() == 0 ? false : true;
    }
}

?>