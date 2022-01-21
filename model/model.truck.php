<?php

class Truck extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllTrucks()
    {
        $query = "SELECT truck_id, truck_no, truck_status FROM truck";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }
 
    public function getTruck($id)
    {
        $query = "SELECT truck_id, truck_no, truck_status FROM truck WHERE truck_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        return $this->fetchRow($stmt);
    }

    public function addTruck($name,$status)
    {
        $query = "INSERT INTO truck(truck_no, truck_status) VALUES(?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "ss", array($name,$status));
        return $this->execute($stmt);
    }

    public function updateTruck($id, $name,$status)
    {
        $query = "UPDATE truck SET truck_no = ?,truck_status = ? WHERE truck_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "ssi", array($name,$status, $id));
        return $this->execute($stmt);
    }

    public function deleteTruck($id)
    {
        $query = "DELETE FROM truck WHERE truck_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        return $this->execute($stmt);
    }

}