<?php

class Returned extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getOrder($slip_number)
    {
        $query = "SELECT * FROM picking_order
                WHERE order_status='deliver' AND slip_no= ?";
        $stmt = $this->prepareQuery($this->conn, $query,"s",array($slip_number));
        return $this->fetchAssoc($stmt);
    }

    public function getAllReturn()
    {
        $query = "SELECT a.stock_id, a.product_id, a.location_id, a.location_type, a.stock_lotno, a.stock_serialno, a.stock_qty, a.stock_expiration_date, a.picking_order_id, a.from_stock_id, a.box_number, a.box_number_Status,b.product_code,b.product_description FROM stock a 
            LEFT JOIN product b ON a.product_id = b.product_id
            WHERE a.box_number_Status = 'return'";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getAllOrdersdetails($slip_id)
    {
        $query = "SELECT a.stock_id AS id,a.picking_order_id AS slip_id,a.product_id,b.product_code,b.product_description,b.product_weight, b.product_length, b.product_width, b.product_height,c.unit_name,a.stock_lotno,a.stock_serialno,a.stock_qty,a.stock_expiration_date
                FROM stock a
                LEFT JOIN product b ON a.product_id = b.product_id
                LEFT JOIN unit c ON b.unit_id = c.unit_id
                WHERE a.picking_order_id = ? AND a.location_type = ? AND a.box_number_Status != 'return'";
                $location_type = "cart";

        $stmt = $this->prepareQuery($this->conn, $query, "is", array($slip_id,$location_type));
        return $this->fetchAssoc($stmt);
    }

    public function getAllLots($slip_id,$product_id,$stock_location)
    {   

        $query = "SELECT a.stock_id,a.product_id,a.stock_lotno,a.stock_serialno,a.stock_qty,a.stock_expiration_date,b.product_code,b.product_description
                FROM stock a
                LEFT JOIN product b ON a.product_id = b.product_id
                WHERE a.product_id = ? AND a.picking_order_id = ? AND a.location_type = ?";

            $stmt = $this->prepareQuery($this->conn, $query, "iis", array($product_id,$slip_id,$stock_location));
            return $this->fetchAssoc($stmt);
        
    }

    public function getQuarantine(){

        $location_type = "Quarantine";
        $query = "SELECT cart_id FROM cart WHERE location_type = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "s", array($location_type));
        $row = $this->fetchRow($stmt);
        return $cart_id = $row[0];

    }


    public function finished_Transaction($slip_id,$user_name)
    {   

        $stat = "deleted";
        $query = "DELETE FROM stock_logs WHERE stock_status = ? AND comments = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($stat,$slip_id));
        $this->execute($stmt);


        $stock_status = "";
        $comments = "";
        $query = "UPDATE stock_logs SET stock_status = ?, comments = ? WHERE stock_status = 'pending' AND comments = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "ssi", array($stock_status,$comments,$slip_id));
        $this->execute($stmt);


        $query = "SELECT stock_id, from_stock_id FROM stock WHERE picking_order_id = ? AND box_number_Status = 'return'";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        $orders = $this->fetchAssoc($stmt);

        foreach($orders as $k=>$v) {

            $stock_id = $v["stock_id"]; 
            $from_stock_id = $v['from_stock_id'];

            $query = "SELECT from_stock_id FROM stock WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($from_stock_id));
            $row = $this->fetchRow($stmt);
            $final_stock_id = $row[0];

            $query = "UPDATE stock SET from_stock_id = ? WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "ii", array($final_stock_id,$stock_id));
            $this->execute($stmt);

        }


        $query = "DELETE FROM stock WHERE picking_order_id = ? AND box_number_Status != 'return'";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        $this->execute($stmt);

        $order_status = "finished";
        $query = "UPDATE picking_order SET order_status = ? WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($order_status,$slip_id));
        $this->execute($stmt);

        $query = "SELECT slip_no FROM picking_order WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        $row = $this->fetchRow($stmt);
        $slip_no = $row[0];

        $audit_action = 'Finished order "'.$slip_no.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);

    }

    public function reship_Order($slip_id)
    {
        
        $order_status = "ship";
        $query = "UPDATE picking_order SET order_status = ? WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($order_status,$slip_id));
        return $this->execute($stmt);

    }


    public function countItemstoCheck($slip_id)
    {
        $checking_status = "";
        $query = "SELECT slip_id FROM picking_order_details WHERE slip_id = ? AND checking_status = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "is", array($slip_id,$checking_status));
        return $this->fetchCount($stmt);

    }

    public function countUndo($slip_id)
    {
        $box_number_Status = "return";
        $query = "SELECT stock_id FROM stock WHERE picking_order_id = ? AND box_number_Status = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "is", array($slip_id,$box_number_Status));
        return $this->fetchCount($stmt);

    }

    

    public function qurantine_Item($returnStockid,$returnQty,$quarantineArea){

        $location_type = "cart";
        $query = "SELECT stock_id,stock_qty,product_id,stock_lotno,stock_serialno,stock_expiration_date,picking_order_id,location_id,box_number,from_stock_id FROM stock WHERE stock_id = ? AND location_type = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "is",array($returnStockid,$location_type));
        $rowq = $this->fetchRow($stmt);
        $stock_id = $rowq[0];
        $stock_qty = $rowq[1];
        $product_id = $rowq[2];
        $stock_lotno = $rowq[3];
        $stock_serialno = $rowq[4];
        $stock_expiration_date = $rowq[5];
        $picking_order_id = $rowq[6];
        $location_id = $rowq[7];
        $box_number = $rowq[8];
        $from_stock_id = $rowq[9];
        $log_type = "out";
        $log_reference = "quarantine area";
        $date_today = date('Y-m-d');

        $remaining_stockqty = $stock_qty - $returnQty;
        if($remaining_stockqty <=0 ){

            $number = $box_number.','.$location_id.','.$from_stock_id;
            $status = "return";
            $query = "INSERT INTO stock SET product_id = ?, location_id = ?, location_type = ?, stock_lotno = ?, stock_serialno = '$stock_serialno', stock_qty = ?, stock_expiration_date = ?, picking_order_id = ?, from_stock_id = ?, box_number = ?, box_number_Status = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "iissisiiss", array($product_id,$quarantineArea,$location_type,$stock_lotno,$returnQty,$stock_expiration_date,$picking_order_id,$stock_id,$number,$status));
            $this->execute($stmt);

            $query = "DELETE FROM stock WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($stock_id));
            $this->execute($stmt);


            $log_type = "out";
            $stock_status = "pending";
            $new_status = "deleted";

            $query = "UPDATE stock_logs SET stock_status = ? WHERE stock_id = ? AND log_type = ? AND stock_status = ? AND comments = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "sissi", array($new_status,$from_stock_id,$log_type,$stock_status,$picking_order_id));
            $this->execute($stmt);


        }else{

            $status = "return";
            $query = "INSERT INTO stock SET product_id = ?, location_id = ?, location_type = ?, stock_lotno = ?, stock_serialno = '$stock_serialno', stock_qty = ?, stock_expiration_date = ?, picking_order_id = ?, from_stock_id = ?, box_number_Status = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "iissisiis", array($product_id,$quarantineArea,$location_type,$stock_lotno,$returnQty,$stock_expiration_date,$picking_order_id,$stock_id,$status));
            $this->execute($stmt);

            $query = "UPDATE stock SET stock_qty = ? WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "ii", array($remaining_stockqty,$stock_id));
            $this->execute($stmt);

            $log_type = "out";
            $stock_status = "pending";

            $query = "UPDATE stock_logs SET log_qty = ? WHERE stock_id = ? AND log_type = ? AND stock_status = ? AND comments = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "iissi", array($remaining_stockqty,$from_stock_id,$log_type,$stock_status,$picking_order_id));
            $this->execute($stmt);

            $query = "INSERT INTO stock_logs SET stock_id = ?, log_type = ?, log_qty = ?, log_reference = ?, log_transaction_date = ?, log_posting_date = ?, stock_status = ?, comments = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "isissssi", array($from_stock_id,$log_type,$returnQty,$log_reference,$date_today,$date_today,$stock_status,$picking_order_id));
            $this->execute($stmt);




        }

        return "success";

    }

    public function qurantine_Order($slip_id,$quarantineArea){

        $location_type = "cart";
        $query = "SELECT stock_id,stock_qty,product_id,stock_lotno,stock_serialno,stock_expiration_date,picking_order_id,location_id,box_number,from_stock_id FROM stock WHERE picking_order_id = ? AND location_type = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "is", array($slip_id,$location_type));
        $orders = $this->fetchAssoc($stmt);

        foreach($orders as $k=>$v) {

            $stock_id = $v["stock_id"];
            $stock_qty = $v["stock_qty"];
            $product_id = $v["product_id"];
            $stock_lotno = $v["stock_lotno"];
            $stock_serialno = $v["stock_serialno"];
            $stock_expiration_date = $v["stock_expiration_date"];
            $picking_order_id = $v["picking_order_id"];
            $location_id = $v["location_id"];
            $box_number = $v["box_number"];
            $from_stock_id = $v["from_stock_id"];
            $log_type = "out";
            $log_reference = "quarantine area";
            $date_today = date('Y-m-d');
            $returnQty = $stock_qty;
            $remaining_stockqty = $stock_qty - $returnQty;


            $number = $box_number.','.$location_id.','.$from_stock_id;
            $status = "return";
            $query = "INSERT INTO stock SET product_id = ?, location_id = ?, location_type = ?, stock_lotno = ?, stock_serialno = '$stock_serialno', stock_qty = ?, stock_expiration_date = ?, picking_order_id = ?, from_stock_id = ?, box_number = ?, box_number_Status = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "iissisiiss", array($product_id,$quarantineArea,$location_type,$stock_lotno,$returnQty,$stock_expiration_date,$picking_order_id,$stock_id,$number,$status));
            $this->execute($stmt);

            $query = "DELETE FROM stock WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($stock_id));
            $this->execute($stmt);


            $log_type = "out";
            $stock_status = "pending";
            $new_status = "deleted";

            $query = "UPDATE stock_logs SET stock_status = ? WHERE stock_id = ? AND log_type = ? AND stock_status = ? AND comments = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "sissi", array($new_status,$from_stock_id,$log_type,$stock_status,$picking_order_id));
            $this->execute($stmt);

        }

        return "success";

    }

    public function undo_Item($stock_id,$stock_qty,$from_stock_id){

        $query = "SELECT stock_id, product_id, location_id, location_type, stock_lotno, stock_serialno, stock_qty, stock_expiration_date, picking_order_id, from_stock_id, box_number, box_number_Status FROM stock WHERE stock_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($stock_id));
        $rowv = $this->fetchRow($stmt);
        $product_id = $rowv[1];
        $stock_lotno = $rowv[4];
        $stock_serialno = $rowv[5];
        $stock_expiration_date = $rowv[7];
        $picking_order_id = $rowv[8];
        $s_id = $rowv[9];
        $number = $rowv[10];
        if($number !=null || $number !=""){
            $var = preg_split("#,#", $number);
            $box_number = $var[0];
            $location_id = $var[1];
            $from_stock_id = $var[2];
        }
        
        $location_type = "cart";
        $box_number_Status = "Moved";

        if($number != null || $number !=""){

            $query = "INSERT INTO stock SET stock_id = ?, product_id = ?, location_id = ?, location_type = ?, stock_lotno = ?, stock_serialno = '$stock_serialno', stock_qty = ?, stock_expiration_date = ?, picking_order_id = ?, from_stock_id = ?, box_number = ?, box_number_Status = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "iiissisiiss",array($s_id,$product_id,$location_id,$location_type,$stock_lotno,$stock_qty,$stock_expiration_date,$picking_order_id,$from_stock_id,$box_number,$box_number_Status));
            $this->execute($stmt);

            $log_type = "out";
            $stock_status = "deleted";
            $new_status = "pending";

            $query = "UPDATE stock_logs SET stock_status = ? WHERE stock_id = ? AND log_type = ? AND stock_status = ? AND comments = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "sissi", array($new_status,$from_stock_id,$log_type,$stock_status,$picking_order_id));
            $this->execute($stmt);

        }else{

            $query = "SELECT stock_qty FROM stock WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($from_stock_id));
            $row = $this->fetchRow($stmt);
            $new_stock_qty = $row[0] + $stock_qty;

            $query = "UPDATE stock SET stock_qty = ? WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "ii", array($new_stock_qty,$from_stock_id));
            $this->execute($stmt);


            $query = "SELECT from_stock_id FROM stock WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "i",array($s_id));
            $rowq = $this->fetchRow($stmt);

            $f_stock_id = $rowq[0];

            $log_type = "out";
            $stock_status = "pending";

            $query = "UPDATE stock_logs SET log_qty = ? WHERE stock_id = ? AND log_type = ? AND stock_status = ? AND comments = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "iissi", array($new_stock_qty,$f_stock_id,$log_type,$stock_status,$picking_order_id));
            $this->execute($stmt);  




        }

        $query = "SELECT from_stock_id FROM stock WHERE stock_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($stock_id));
        $row1 = $this->fetchRow($stmt);
        $pre_stockid = $row1[0];

        $query = "SELECT from_stock_id FROM stock WHERE stock_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($pre_stockid));
        $row2 = $this->fetchRow($stmt);
        $final_stockid = $row2[0];

        $log_type = "out";
        $stock_status = "pending";
        $log_reference = "quarantine area";
        $query = "DELETE FROM stock_logs WHERE stock_id = ? AND log_type = ? AND log_reference = ? AND stock_status = ? AND comments = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "isssi", array($final_stockid,$log_type,$log_reference,$stock_status,$picking_order_id));
        $this->execute($stmt);
     

        $query = "DELETE FROM stock WHERE stock_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($stock_id));
        $this->execute($stmt);

        return "success";

    }


}