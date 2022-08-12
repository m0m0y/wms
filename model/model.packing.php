<?php

class Packing extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getOrder($slip_number)
    {
        $query = "SELECT * FROM picking_order
                WHERE order_status='pack' AND slip_no= ?";
        $stmt = $this->prepareQuery($this->conn, $query,"s",array($slip_number));
        return $this->fetchAssoc($stmt);
    }

    public function getAllOrders()
    {
        $query = "SELECT * FROM picking_order
                WHERE order_status='pack'";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getAllOrdersdetails($slip_id)
    {
        $query = "SELECT a.id,a.slip_id,a.product_id,b.product_code,b.product_description,c.unit_name,a.quantity_order,a.quantity_shipped,a.location,a.order_status,d.box_number,d.stock_lotno,d.stock_qty,d.stock_id
                FROM picking_order_details a
                LEFT JOIN product b ON a.product_id = b.product_id
                LEFT JOIN unit c ON b.unit_id = c.unit_id
                LEFT JOIN stock d ON a.slip_id = d.picking_order_id AND a.product_id = d.product_id
                WHERE a.slip_id = ? GROUP BY d.stock_id ORDER BY b.product_code ASC";

// SELECT a.id,a.slip_id,a.product_id,b.product_code,b.product_description,c.unit_name,a.quantity_order,a.quantity_shipped,a.location,a.order_status,a.box_number,d.stock_lotno
//                 FROM picking_order_details a
//                 LEFT JOIN product b ON a.product_id = b.product_id
//                 LEFT JOIN unit c ON b.unit_id = c.unit_id
//                 LEFT JOIN stock d ON a.slip_id = d.picking_order_id AND a.product_id = d.product_id
//                 WHERE a.slip_id = ? GROUP BY a.id ORDER BY a.box_number ASC
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        return $this->fetchAssoc($stmt);
    }

    public function getAllBoxes($slip_id)
    {
        $query = "SELECT box_number FROM stock WHERE picking_order_id = ? GROUP BY box_number ORDER BY box_number ASC";
        $stmt = $this->prepareQuery($this->conn, $query ,"i", array($slip_id));
        return $this->fetchAssoc($stmt);
    }

    public function getAllOrdersdetailsReport($slip_id,$box_no)
    {
        $query = "SELECT a.id,a.slip_id,a.product_id,b.product_code,b.product_description,c.unit_name,a.quantity_order,a.quantity_shipped,a.location,a.order_status,d.box_number,d.stock_lotno,SUM(d.stock_qty) AS stock_qty,d.stock_id
                FROM picking_order_details a
                LEFT JOIN product b ON a.product_id = b.product_id
                LEFT JOIN unit c ON b.unit_id = c.unit_id
                LEFT JOIN stock d ON a.slip_id = d.picking_order_id AND a.product_id = d.product_id
                WHERE a.slip_id = ? AND d.box_number = ? GROUP BY a.id ORDER BY a.box_number ASC";


                // SELECT a.id,a.slip_id,a.product_id,b.product_code,b.product_description,c.unit_name,a.quantity_order,a.quantity_shipped,a.location,a.order_status,a.box_number,d.stock_lotno
                // FROM picking_order_details a
                // LEFT JOIN product b ON a.product_id = b.product_id
                // LEFT JOIN unit c ON b.unit_id = c.unit_id
                // LEFT JOIN stock d ON a.slip_id = d.picking_order_id AND a.product_id = d.product_id
                // WHERE a.slip_id = ? AND a.box_number = ? GROUP BY a.id ORDER BY a.box_number ASC

        $stmt = $this->prepareQuery($this->conn, $query, "is", array($slip_id,$box_no));
        return $this->fetchAssoc($stmt);
    }

    public function ship_Order($slip_id,$comments,$user_name)
    {
        
        $order_status = "ship";
        $query = "UPDATE picking_order SET order_status = ? WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($order_status,$slip_id));
        $this->execute($stmt);

        $query = "SELECT slip_no FROM picking_order WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        $row = $this->fetchRow($stmt);
        $slip_no = $row[0];

        $audit_action = 'Send order "'.$slip_no.'" to dispatching area';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);

    }

    public function check_Order($slip_id,$comments,$user_name)
    {
        
        $order_status = "checking";
        $query = "UPDATE picking_order SET order_status = ? WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($order_status,$slip_id));
        $this->execute($stmt);

        $query = "SELECT slip_no FROM picking_order WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        $row = $this->fetchRow($stmt);
        $slip_no = $row[0];

        $audit_action = 'Send order "'.$slip_no.'" to checking area (recheck)';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);

    }

    public function box_Order($box_number,$picking_order_ids){

        $id = explode(",",$picking_order_ids);
        $count_id = count($id);

        $i=0;
        for($i==0;$i<$count_id;$i++){
            $picked_id = $id[$i];
            // $query = "UPDATE picking_order_details SET box_number = ? WHERE id = ?";
            // $stmt = $this->prepareQuery($this->conn, $query, "si", array($box_number,$picked_id));
            // $this->execute($stmt);

            $query = "UPDATE stock SET box_number = ? WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "si", array($box_number,$picked_id));
            $this->execute($stmt);
        }

        return "true";

    }


    public function undo_BoxedItem($id){

        $box_number = "";
        $query = "UPDATE stock SET box_number = ? WHERE stock_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($box_number,$id));
        return $this->execute($stmt);
        // $query = "UPDATE picking_order_details SET box_number = ? WHERE id = ?";
        // $stmt = $this->prepareQuery($this->conn, $query, "si", array($box_number,$id));
        // return $this->execute($stmt);

    }

    public function undoall_BoxedItem($slip_id){

        $box_number = "";
        $query = "UPDATE stock SET box_number = ? WHERE picking_order_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($box_number,$slip_id));
        return $this->execute($stmt);
        // $query = "UPDATE picking_order_details SET box_number = ? WHERE id = ?";
        // $stmt = $this->prepareQuery($this->conn, $query, "si", array($box_number,$id));
        // return $this->execute($stmt);

    }

    public function getAllBoxno($slip_id)
    {
        $query = "SELECT box_number FROM stock WHERE picking_order_id = ? AND box_number !='' GROUP BY box_number";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        return $this->fetchAssoc($stmt);
    }

    public function getUnboxitem($slip_id)
    {
        $query = "SELECT box_number FROM stock WHERE picking_order_id = ? AND box_number =''";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        return $this->fetchCount($stmt);
    }

}