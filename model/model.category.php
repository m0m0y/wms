<?php

class Category extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllCategory()
    {
        $query = "SELECT category_id, category_name FROM category";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }
 
    public function getCategory($id)
    {
        $query = "SELECT category_id, category_name FROM category WHERE category_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        return $this->fetchRow($stmt);
    }

    public function addCategory($name, $user_name)
    {
        $query = "INSERT INTO category(category_name) VALUES(?)";
        $stmt = $this->prepareQuery($this->conn, $query, "s", array($name));
        $this->execute($stmt);

        $audit_action = 'Added new category "'.$name.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

    public function updateCategory($id, $name, $user_name)
    {
        $query = "SELECT category_name FROM category WHERE category_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $row = $this->fetchRow($stmt);
        $category_name = $row[0];

        $query = "UPDATE category SET category_name = ? WHERE category_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($name, $id));
        $this->execute($stmt);

        $audit_action = 'Edit category "'.$category_name.'" to "'.$name.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

    public function deleteCategory($id, $user_name)
    {
        $query = "SELECT category_name FROM category WHERE category_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $row = $this->fetchRow($stmt);
        $category_name = $row[0];

        $query = "DELETE FROM category WHERE category_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $this->execute($stmt);

        $audit_action = 'Delete category "'.$category_name.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

}