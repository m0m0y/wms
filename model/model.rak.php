<?php

class Rak extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllRaks()
    {
        $query = "SELECT rak_id, rak_name, rak_column, rak_level FROM rak";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }
 
    public function getRak($id)
    {
        $query = "SELECT rak_id, rak_name, rak_column, rak_level FROM rak WHERE rak_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        return $this->fetchRow($stmt);
    }

    public function addRak($name,$column,$level,$user_name)
    {
        $query = "INSERT INTO rak(rak_name, rak_column, rak_level) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($name,$column,$level));
        $this->execute($stmt);

        $rakname = $name.$column.$level;
        $audit_action = 'Added new rak "'.$rakname.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

    public function updateRak($id, $name,$column,$level,$user_name)
    {
        $query = "SELECT rak_name, rak_column, rak_level FROM rak WHERE rak_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $row = $this->fetchRow($stmt);
        $rakname = $row[0].$row[1].$row[2];

        $query = "UPDATE rak SET rak_name = ?,rak_column = ?,rak_level = ? WHERE rak_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "sssi", array($name,$column,$level, $id));
        $this->execute($stmt);

        $audit_action = 'Edit rak "'.$rakname.'" to "'.$name.$column.$level.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

    public function deleteRak($id,$user_name)
    {
        $query = "SELECT rak_name, rak_column, rak_level FROM rak WHERE rak_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $row = $this->fetchRow($stmt);
        $rakname = $row[0].$row[1].$row[2];

        $query = "DELETE FROM rak WHERE rak_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $this->execute($stmt);

        $audit_action = 'Delete rak "'.$rakname.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

}