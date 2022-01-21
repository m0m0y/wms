<?php

class Product extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllProducts()
    {
        $query = "SELECT 
        p.product_id, p.category_id, 
        p.unit_id, p.product_type, 
        p.product_code, p.product_description,
        p.product_weight, p.product_length, p.product_width, p.product_height, 
        p.product_expiration, u.unit_name, c.category_name FROM product p LEFT JOIN
        unit u ON p.unit_id = u.unit_id LEFT JOIN category c ON p.category_id = c.category_id";

        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }
 
    public function getProduct($id)
    {
        $query = "SELECT product_id, category_id, unit_id, product_type, product_code, product_description, product_expiration FROM product WHERE product_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        return $this->fetchRow($stmt);
    }

    public function addProduct($category_id,$unit_id,$product_type,$product_code,$product_description,$product_weight,$product_length,$product_width,$product_height,$product_expiration,$user_name)
    {
        $query = "INSERT INTO product(category_id, unit_id, product_type, product_code, product_description,product_weight,product_length,product_width,product_height, product_expiration) VALUES(?,?,?,?,?,?,?,?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "iissssssss", array($category_id,$unit_id,$product_type,$product_code,$product_description,$product_weight,$product_length,$product_width,$product_height,$product_expiration));
        $this->execute($stmt);

        $audit_action = 'Added new product "'.$product_code.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

    public function updateProduct($category_id,$unit_id,$product_type,$product_code,$product_description,$product_weight,$product_length,$product_width,$product_height,$product_expiration,$id,$user_name)
    {
        $query = "SELECT product_code FROM product WHERE product_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $row = $this->fetchRow($stmt);
        $p_code = $row[0];

        $query = "UPDATE product SET category_id = ?,unit_id = ?, product_type = ?, product_code = ?, product_description = ?, product_weight = ?, product_length = ?, product_width = ?, product_height = ?, product_expiration = ? WHERE product_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "iissssssssi", array($category_id,$unit_id,$product_type,$product_code,$product_description,$product_weight,$product_length,$product_width,$product_height,$product_expiration,$id));
        $this->execute($stmt);

        $audit_action = 'Edit product "'.$p_code.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

    public function deleteProduct($id,$user_name)
    {
        $query = "SELECT product_code FROM product WHERE product_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $row = $this->fetchRow($stmt);
        $p_code = $row[0];

        $query = "DELETE FROM product WHERE product_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $this->execute($stmt);

        $audit_action = 'Delete product "'.$p_code.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

}