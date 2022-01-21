 <?php

class Shipping extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllOrders()
    {
        $query = "SELECT * FROM picking_order 
                WHERE order_status='ship' GROUP BY slip_id";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getAllDispatcher()
    {
        $query = "SELECT user_id, user_fullname FROM user_account WHERE user_usertype = 'dispatcher' ";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getAllBox($slip_id)
    {
        $query = "SELECT box_number,box_number_Status FROM stock WHERE picking_order_id = ? GROUP BY box_number";

                // SELECT a.id,a.slip_id,a.product_id,b.product_code,b.product_description,c.unit_name,a.quantity_order,a.quantity_shipped,a.location,a.order_status,a.stock_id,a.box_number,a.box_number_Status
                // FROM picking_order_details a
                // LEFT JOIN product b ON a.product_id = b.product_id
                // LEFT JOIN unit c ON b.unit_id = c.unit_id WHERE a.slip_id = ?
                // GROUP BY a.box_number
                // ORDER BY a.box_number_Status ASC
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        return $this->fetchAssoc($stmt);
    }

    public function getOrderStatus($slip_id)
    {
        $query = "SELECT order_status FROM picking_order WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        
        $data = $this->fetchRow($stmt);
        if(!$data) { return false; }

        switch($data[0]) {
            case "repick":
            case "ship":
                return $this->getAllBox($slip_id);
                break;
            default:
                return false;    
        }        
    }

    public function Save_selectedItem($box_number,$pickingCart)
    {

        $location_type = "cart";

        $qry = "SELECT picking_order_id,product_id FROM stock WHERE box_number = ?";
        $stmt = $this->prepareQuery($this->conn, $qry, "s", array($box_number));
        $orders = $this->fetchAssoc($stmt);

        foreach($orders as $k=>$v) {

          $s_id = $v["picking_order_id"]; 
          $p_id = $v['product_id'];

            $query = "UPDATE stock SET location_id = ? WHERE picking_order_id = ? AND product_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "iii", array($pickingCart,$s_id,$p_id));
            $this->execute($stmt);

        }

        $query2 = "UPDATE stock SET box_number_Status = 'Moved' WHERE box_number = ?";
        $stmt = $this->prepareQuery($this->conn, $query2, "s", array($box_number));
        $this->execute($stmt);


        return 'success';

    }

    public function Undo_pickedorder($box_number,$rak_return_id)
    {
        
        $location_type = "cart";

        $qry = "SELECT picking_order_id,product_id FROM stock WHERE box_number = ?";
        $stmt = $this->prepareQuery($this->conn, $qry, "s", array($box_number));
        $orders = $this->fetchAssoc($stmt);

        foreach($orders as $k=>$v) {

          $s_id = $v["picking_order_id"]; 
          $p_id = $v['product_id'];

            $query = "UPDATE stock SET location_id = ? WHERE picking_order_id = ? AND product_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "iii", array($rak_return_id,$s_id,$p_id));
            $this->execute($stmt);

        }

        $query2 = "UPDATE stock SET box_number_Status = '' WHERE box_number = ?";
        $stmt = $this->prepareQuery($this->conn, $query2, "s", array($box_number));
        $this->execute($stmt);


        return 'success';

    }

    public function repack_order($slip_id){

        $order_status = "pack";
        $query = "UPDATE picking_order SET order_status = ? WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($order_status,$slip_id));
        return $this->execute($stmt);

    }

    public function countPickedOrder($slip_id)
    {
        $order_status = "pending";
        $query = "SELECT slip_id FROM picking_order_details WHERE slip_id = ? AND order_status = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "is", array($slip_id,$order_status));
        return $this->fetchCount($stmt);

    }

    public function countBoxPerOrder($slip_id)
    {
        $query = "SELECT box_number_Status FROM stock WHERE picking_order_id = ? GROUP BY box_number";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        return $this->fetchCount($stmt);

    }

    public function countpickedBox($slip_id)
    {
        $order_status = "";
        $query = "SELECT box_number_Status FROM stock WHERE picking_order_id = ? AND box_number_Status != ? GROUP BY box_number";
        $stmt = $this->prepareQuery($this->conn, $query, "is", array($slip_id,$order_status));
        return $this->fetchCount($stmt);

    }

    public function validateStorage($id,$type)
    {
        $query = "SELECT cart_id FROM cart WHERE cart_id = ? AND location_type = ? LIMIT 1";
        $stmt = $this->prepareQuery($this->conn, $query, "is", array($id,$type));
        $result = $this->fetchCount($stmt);
        return ($result) ? true : false;
    }

    public function deliverOrder($slip_id,$do_number,$assign_to,$user_name){

        $order_status = "deliver";
        $tracking_status = "Out For Delivery";
        $query = "UPDATE picking_order SET order_status = ?, do_no = ?, tracking_status = ?, assign_to = ? WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "ssssi", array($order_status,$do_number,$tracking_status,$assign_to,$slip_id));
        $this->execute($stmt);

        $query = "SELECT slip_no FROM picking_order WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        $row = $this->fetchRow($stmt);
        $slip_no = $row[0];

        $audit_action = 'Send order "'.$slip_no.'" to truck for deliver';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);

    }


}