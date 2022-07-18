<?php

class Stockcard extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllProducts()
    {
        $query = "SELECT product_id, category_id, unit_id, product_type, product_code, product_description, product_expiration FROM product ORDER BY product_code ASC";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getAllLots($product)
    {
        $query = "SELECT stock_id, product_id, location_id, location_type, stock_lotno, stock_serialno, stock_qty, stock_expiration_date FROM stock WHERE product_id = ? AND location_type = 'rak'";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($product));
        return $this->fetchAssoc($stmt);
    }

    public function getProduct_expiry($dateFrom,$dateTo)
    {
        $query = "SELECT a.product_code,a.product_description,c.unit_name,b.stock_lotno,b.stock_expiration_date,b.stock_qty FROM stock b
                  LEFT JOIN product a ON a.product_id = b.product_id
                  LEFT JOIN unit c ON a.unit_id = c.unit_id
                  WHERE b.stock_expiration_date BETWEEN ? AND ? ORDER BY a.product_code ASC";
        $stmt = $this->prepareQuery($this->conn, $query, "ss", array($dateFrom,$dateTo));
        return $this->fetchAssoc($stmt);
    }

    public function getQuarantinedItems(){
        $query = "SELECT a.stock_id,a.product_id,a.stock_lotno,a.stock_serialno,a.stock_qty,a.stock_expiration_date,b.product_code,b.product_description,c.unit_name,d.slip_order_date FROM stock a
                  LEFT JOIN product b ON a.product_id = b.product_id
                  LEFT JOIN unit c ON b.unit_id = c.unit_id
                  LEFT JOIN picking_order d ON a.picking_order_id = d.slip_id
                  WHERE a.box_number_Status ='return' ORDER BY b.product_code ASC";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getSummary()
    {
        $query = "SELECT a.product_code,a.product_description,b.unit_name,c.category_name,SUM(d.stock_qty) AS quantity
                  FROM product a
                  LEFT JOIN unit b ON a.unit_id = b.unit_id
                  LEFT JOIN category c ON a.category_id = c.category_id
                  LEFT JOIN stock d ON a.product_id = d.product_id
                  GROUP BY a.product_id
                  ORDER BY a.product_code ASC";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getSummaryWithlots()
    {
        $query = "SELECT a.product_code,a.product_description,b.unit_name,c.category_name,sum(d.stock_qty) AS quantity,d.stock_lotno,d.stock_expiration_date
                  FROM product a
                  LEFT JOIN unit b ON a.unit_id = b.unit_id
                  LEFT JOIN category c ON a.category_id = c.category_id
                  LEFT JOIN stock d ON a.product_id = d.product_id
                  WHERE d.stock_qty > '0'
                  GROUP BY d.stock_lotno, a.product_id
                  ORDER BY a.product_code ASC";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getStockcard($stock_id)
    {
        $query = "SELECT a.*,b.stock_lotno,b.stock_serialno,b.stock_expiration_date,b.stock_qty FROM stock_logs a
                  LEFT JOIN stock b ON a.stock_id = b.stock_id 
                  WHERE a.stock_id = ? AND a.stock_status !='pending' AND a.stock_status !='deleted'";
        $stmt = $this->prepareQuery($this->conn, $query, "i",array($stock_id));
        return $this->fetchAssoc($stmt);

    }

    public function getProductDetails($product_id)
    {
        $query = "SELECT a.product_code,a.product_description,b.unit_name FROM product a 
                  LEFT JOIN unit b ON a.unit_id = b.unit_id
                  WHERE a.product_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($product_id));
        $row = $this->fetchRow($stmt);
        $unit = $row[2];
        $product_code = $row[0];
        $product_description = $row[1];

        return array("unit"=>$unit,"product_code"=>$product_code,"product_description"=>$product_description);

    }

    public function getlotno($stock_id)
    {
        $query = "SELECT stock_lotno FROM stock 
                  WHERE stock_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($stock_id));
        $row = $this->fetchRow($stmt);
        $lotno = $row[0];

        return $lotno;

    }
 

}