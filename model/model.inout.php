<?php

class Inout extends DBHandler { 

    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllProductCodes()
    {
        $query = "SELECT product_id, product_code, product_description FROM product";

        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getUnitProduct($product_id)
    {
        $query = "SELECT p.product_id, u.unit_name, SUM(s.stock_qty) as stock_quantity FROM unit u LEFT JOIN product p ON p.unit_id = u.unit_id LEFT JOIN stock s ON p.product_id = s.product_id WHERE p.product_id = '$product_id'";

        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getLotnumbers($product_id) 
    {
        $query = "SELECT s.stock_id, s.stock_expiration_date, s.stock_lotno FROM stock s LEFT JOIN product p ON p.product_id = s.product_id WHERE p.product_id = ?";

        $stmt = $this->prepareQuery($this->conn, $query, "i", [$product_id]);
        return $this->fetchAssoc($stmt);
    }

    public function getTotlaQty($stock_id) {
        $query = "SELECT s.stock_id, s.stock_expiration_date, s.stock_lotno, l.log_id, l.log_type, l.log_qty FROM stock s LEFT JOIN stock_logs l ON s.stock_id = l.stock_id WHERE s.stock_id = ?";

        $stmt = $this->prepareQuery($this->conn, $query, "i", [$stock_id]);
        return $this->fetchAssoc($stmt);
    }

}