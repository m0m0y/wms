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
        $query = "SELECT p.product_id, u.unit_name, SUM(s.stock_qty) as total_stock_qty FROM unit u LEFT JOIN product p ON p.unit_id = u.unit_id LEFT JOIN stock s ON p.product_id = s.product_id WHERE p.product_id = ?";

        $stmt = $this->prepareQuery($this->conn, $query, "i", [$product_id]);
        return $this->fetchAssoc($stmt);
    }

    public function getLotnumbers($product_id) 
    {
        $query = "SELECT s.stock_id, s.stock_expiration_date, s.stock_lotno, s.stock_serialno FROM stock s LEFT JOIN product p ON p.product_id = s.product_id WHERE p.product_id = ? AND s.location_type = 'rak' AND stock_qty!=0";

        $stmt = $this->prepareQuery($this->conn, $query, "i", [$product_id]);
        return $this->fetchAssoc($stmt);
    }

    public function getTotlaQty($stock_id)
    {
        $query = "SELECT s.stock_id, s.stock_expiration_date, s.stock_lotno, s.stock_qty, l.log_id, l.log_type, l.log_qty, l.log_transaction_date FROM stock s LEFT JOIN stock_logs l ON s.stock_id = l.stock_id WHERE s.stock_id = ? AND l.log_type='in' AND s.stock_qty != 0";

        $stmt = $this->prepareQuery($this->conn, $query, "i", [$stock_id]);
        return $this->fetchAssoc($stmt);
    }

    public function getSearchProductCodes($product_code)
    {
        $query = "SELECT * FROM product WHERE product_code like '%$product_code%' OR product_description like '%$product_code%' LIMIT 1";

        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getProductCodeWhere($product_code)
    {
        $query = "SELECT product_id, product_code, product_description FROM product WHERE product_code like '%$product_code%' OR product_description like '%$product_code%' LIMIT 1";

        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function searchUnitProduct($product_id)
    {
        $query = "SELECT p.product_id, u.unit_name, SUM(s.stock_qty) as stock_quantity FROM unit u LEFT JOIN product p ON p.unit_id = u.unit_id LEFT JOIN stock s ON p.product_id = s.product_id WHERE s.location_type = 'rak' AND p.product_id like '%$product_id%' LIMIT 1";

        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function outQuantity($pcode,$unit,$stockQuantity,$lotno,$expDate,$qty_per_lot,$quantity,$transacDate,$user_name)
    {
        $totalQuantity = $qty_per_lot-$quantity;

        $query = "UPDATE stock_logs SET log_qty = ?, log_transaction_date = ? WHERE stock_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "ssi", array($totalQuantity,$transacDate,$lotno));
        $this->execute($stmt);

        $query = "UPDATE stock SET stock_qty = ? WHERE stock_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($totalQuantity,$lotno));
        $this->execute($stmt);

        $query = "SELECT product_code FROM product WHERE product_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($pcode));
        $row = $this->fetchRow($stmt);
        $product_code = $row[0];

        $query = "SELECT stock_lotno FROM stock WHERE stock_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($lotno));
        $row = $this->fetchRow($stmt);
        $lotno = $row[0];

        $audit_action = 'Out '.$quantity.' "'.$product_code.' ('.$lotno.')"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        $this->execute($stmt);
    }
}