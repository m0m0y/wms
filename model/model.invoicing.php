<?php

class Invoicing extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllOrders()
    {
        $query = "SELECT * FROM picking_order
                WHERE order_status='invoice'";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getAllOrdersdetails($slip_id)
    {
        $query = "SELECT a.id,a.slip_id,a.product_id,b.product_code,b.product_description,c.unit_name,a.quantity_order,a.quantity_shipped,a.location,a.order_status,a.stock_id
                FROM picking_order_details a
                LEFT JOIN product b ON a.product_id = b.product_id
                LEFT JOIN unit c ON b.unit_id = c.unit_id WHERE a.slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        return $this->fetchAssoc($stmt);
    }

    public function getAllLots($slip_id,$product_id,$stock_location)
    {   

        // if($stock_location=="rak"){

        //     $query = "SELECT a.stock_id, a.product_id, a.stock_lotno, a.stock_serialno, a.stock_qty, a.stock_expiration_date,b.rak_name,b.rak_column,b.rak_level,c.location_name
        //         FROM stock a
        //         LEFT JOIN rak b ON a.location_id = b.rak_id
        //         LEFT JOIN cart c ON a.location_id = c.cart_id
        //         WHERE a.product_id= ? AND a.location_type= ? ORDER BY a.stock_expiration_date ASC LIMIT 1";
        //     $stmt = $this->prepareQuery($this->conn, $query, "is", array($product_id,$stock_location));
        //     return $this->fetchAssoc($stmt);


        // }else{

            $query = "SELECT a.stock_id, a.product_id, a.stock_lotno, a.stock_serialno, SUM(a.stock_qty) AS stock_qty, a.stock_expiration_date,a.location_type AS loc,a.picking_order_id,a.from_stock_id,b.rak_name,b.rak_column,b.rak_level,c.location_name,c.location_type
                FROM stock a
                LEFT JOIN rak b ON a.location_id = b.rak_id
                LEFT JOIN cart c ON a.location_id = c.cart_id
                WHERE a.product_id= ? AND a.picking_order_id = ? AND a.location_type= ? 
                GROUP BY a.stock_lotno
                ORDER BY a.stock_expiration_date ASC,loc DESC";
            $stmt = $this->prepareQuery($this->conn, $query, "iis", array($product_id,$slip_id,$stock_location));
            return $this->fetchAssoc($stmt);

        // }
        
    }


    public function repick_Order($slip_id,$comments,$user_name)
    {
        
        $order_status = "repick";
        $query = "UPDATE picking_order SET comments = ?,order_status = ? WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "ssi", array($comments,$order_status,$slip_id));
        $this->execute($stmt);

        $query = "SELECT slip_no FROM picking_order WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        $row = $this->fetchRow($stmt);
        $slip_no = $row[0];

        $audit_action = 'Send order "'.$slip_no.'" to warehouse to repick';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);

    }


    public function check_Order($slip_id,$user_name)
    {
        
        $order_status = "checking";
        $query = "UPDATE picking_order SET order_status = ? WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($order_status,$slip_id));
        $this->execute($stmt);

        $query = "SELECT slip_no FROM picking_order WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        $row = $this->fetchRow($stmt);
        $slip_no = $row[0];

        $audit_action = 'Send order "'.$slip_no.'" to warehouse for validation';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);

    }

    public function countInvoices()
    {
        $query = "SELECT order_status FROM picking_order WHERE order_status = 'invoice'";
        return $this->numRows($this->conn, $query);
    }


}