<?php

class Unit extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllUnits()
    {
        $query = "SELECT unit_id, unit_name FROM unit";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }
 
    public function getUnit($id)
    {
        $query = "SELECT unit_id, unit_name FROM unit WHERE unit_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        return $this->fetchRow($stmt);
    }

    public function addUnit($name,$user_name)
    {
        $query = "INSERT INTO unit(unit_name) VALUES(?)";
        $stmt = $this->prepareQuery($this->conn, $query, "s", array($name));
        $this->execute($stmt);

        $audit_action = 'Added new unit "'.$name.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

    public function updateUnit($id, $name, $user_name)
    {
        $query = "SELECT unit_name FROM unit WHERE unit_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $row = $this->fetchRow($stmt);
        $unit_name = $row[0];

        $query = "UPDATE unit SET unit_name = ? WHERE unit_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($name, $id));
        $this->execute($stmt);

        $audit_action = 'Edit unit "'.$unit_name.'" to "'.$name.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

    public function deleteUnit($id, $user_name)
    {
        $query = "SELECT unit_name FROM unit WHERE unit_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $row = $this->fetchRow($stmt);
        $unit_name = $row[0];

        $query = "DELETE FROM unit WHERE unit_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $this->execute($stmt);

        $audit_action = 'Delete unit "'.$unit_name.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

}