<?php

class Customer extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllCustomers()
    {
        $query = "SELECT customer_id, customer_name, customer_contactno, customer_address FROM customer";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }
 
    public function getCustomer($id)
    {
        $query = "SELECT customer_id, customer_name, customer_contactno, customer_address FROM customer WHERE customer_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        return $this->fetchRow($stmt);
    }

    public function addCustomer($name,$contactno,$address)
    {
        $query = "INSERT INTO customer(customer_name, customer_contactno, customer_address) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($name,$contactno,$address));
        return $this->execute($stmt);
    }

    public function updateCustomer($id, $name,$contactno,$address)
    {
        $query = "UPDATE customer SET customer_name = ?,customer_contactno = ?,customer_address = ? WHERE customer_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "sssi", array($name,$contactno,$address, $id));
        return $this->execute($stmt);
    }

    public function deleteCustomer($id)
    {
        $query = "DELETE FROM customer WHERE customer_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        return $this->execute($stmt);
    }

}