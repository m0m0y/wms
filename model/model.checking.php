<?php

class Checking extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getOrder($slip_number)
    {
        $query = "SELECT * FROM picking_order
                WHERE order_status='checking' AND slip_no= ?";
        $stmt = $this->prepareQuery($this->conn, $query,"s",array($slip_number));
        return $this->fetchAssoc($stmt);
    }

    public function getAllOrdersdetails($slip_id)
    {
        $query = "SELECT a.id,a.slip_id,a.product_id,b.product_code,b.product_description,b.product_weight, b.product_length, b.product_width, b.product_height,c.unit_name,a.quantity_order,a.quantity_shipped,a.location,a.order_status,a.stock_id,a.checking_status
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

        //     $query = "SELECT a.stock_id, a.product_id, a.stock_lotno, a.stock_serialno, a.stock_qty, a.stock_expiration_date,b.rak_name,b.rak_column,b.rak_level,c.location_name
        //         FROM stock a
        //         LEFT JOIN rak b ON a.location_id = b.rak_id
        //         LEFT JOIN cart c ON a.location_id = c.cart_id
        //         WHERE a.stock_id= ? AND a.location_type= ? ORDER BY a.stock_expiration_date ASC LIMIT 1";
        //     $stmt = $this->prepareQuery($this->conn, $query, "is", array($product_id,$stock_location));
        //     return $this->fetchAssoc($stmt);

        // }

        $query = "SELECT a.stock_id, a.product_id, a.stock_lotno, a.stock_serialno, SUM(a.stock_qty) AS stock_qty, a.stock_expiration_date,a.location_type AS loc,a.picking_order_id,a.from_stock_id,b.rak_name,b.rak_column,b.rak_level,c.location_name,c.location_type
                FROM stock a
                LEFT JOIN rak b ON a.location_id = b.rak_id
                LEFT JOIN cart c ON a.location_id = c.cart_id
                WHERE a.product_id= ? AND a.picking_order_id = ? AND a.location_type= ? 
                GROUP BY a.stock_lotno
                ORDER BY a.stock_expiration_date ASC,loc DESC";
            $stmt = $this->prepareQuery($this->conn, $query, "iis", array($product_id,$slip_id,$stock_location));
            return $this->fetchAssoc($stmt);
        
    }


    public function pack_Order($slip_id,$user_name)
    {
        
        $order_status = "pack";
        $query = "UPDATE picking_order SET order_status = ? WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($order_status,$slip_id));
        $this->execute($stmt);

        $query = "SELECT slip_no FROM picking_order WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        $row = $this->fetchRow($stmt);
        $slip_no = $row[0];

        $audit_action = 'Send order "'.$slip_no.'" to packing area';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);

    }

    public function invoice_Order($slip_id,$user_name)
    {
        
        $order_status = "invoice";
        $query = "UPDATE picking_order SET order_status = ? WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($order_status,$slip_id));
        $this->execute($stmt);

        $query = "SELECT slip_no FROM picking_order WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        $row = $this->fetchRow($stmt);
        $slip_no = $row[0];

        $audit_action = 'Send order "'.$slip_no.'" to ccd for re-invoice';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);

    }

    public function approve_orderitem($id){

        $item_status = "approved";
        $query = "UPDATE picking_order_details SET checking_status = ? WHERE id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($item_status,$id));
        return $this->execute($stmt);

    }

    public function undo_orderitem($id){

        $item_status = "";
        $query = "UPDATE picking_order_details SET checking_status = ? WHERE id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($item_status,$id));
        return $this->execute($stmt);

    }

    public function countItemstoCheck($slip_id)
    {
        $checking_status = "";
        $query = "SELECT slip_id FROM picking_order_details WHERE slip_id = ? AND checking_status = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "is", array($slip_id,$checking_status));
        return $this->fetchCount($stmt);

    }

    public function search($term)
    {
        $term = "%" . $term . "%";
        $query = "SELECT slip_no, order_status FROM picking_order WHERE slip_no LIKE ? LIMIT 10";
        $stmt = $this->prepareQuery($this->conn, $query, "s", array($term));
        return $this->fetchAssoc($stmt);
    }


}