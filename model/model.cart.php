<?php

class Cart extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllCarts()
    {
        $query = "SELECT cart_id, location_name, location_type, location_status FROM cart";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }
 
    public function getCart($id)
    {
        $query = "SELECT cart_id, cart_name, cart_status FROM cart WHERE cart_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        return $this->fetchRow($stmt);
    }

    public function addCart($location_name,$location_type,$status,$user_name)
    {
        $query = "INSERT INTO cart(location_name, location_type, location_status) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($location_name,$location_type,$status));
        $this->execute($stmt);

        $audit_action = 'Added new location "'.$location_name.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

    public function updateCart($id,$location_name,$location_type,$status,$user_name)
    {
        $query = "SELECT location_name FROM cart WHERE cart_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $row = $this->fetchRow($stmt);
        $name = $row[0];

        $query = "UPDATE cart SET location_name = ?,location_type = ?,location_status = ? WHERE cart_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "sssi", array($location_name,$location_type,$status, $id));
        $this->execute($stmt);

        $audit_action = 'Edit location "'.$name.'" to "'.$location_name.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

    public function deleteCart($id,$user_name)
    {
        $query = "SELECT location_name FROM cart WHERE cart_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $row = $this->fetchRow($stmt);
        $name = $row[0];

        $query = "DELETE FROM cart WHERE cart_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $this->execute($stmt);

        $audit_action = 'Delete location "'.$name.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

    public function getCartOnly()
    {
        $location_type = "Cart";
        $query = "SELECT cart_id, location_name, location_type, location_status FROM cart WHERE location_type='$location_type'";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getTableOnly()
    {
        $location_type = "Table";
        $query = "SELECT cart_id, location_name, location_type, location_status FROM cart WHERE location_type='$location_type'";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getTruckOnly()
    {
        $location_type = "Truck";
        $query = "SELECT cart_id, location_name, location_type, location_status FROM cart WHERE location_type='$location_type'";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

}