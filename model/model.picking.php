<?php

class Picking extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllUsers()
    {
        $query = "SELECT user_id, user_fullname FROM user_account WHERE user_usertype='picker'";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function save_user($user_id,$slip_id)
    {

        $query = "UPDATE picking_order SET user_id = ? WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "ii", array($user_id,$slip_id));
        return $this->execute($stmt);

    }

    public function deleteOrder($slip_id){

        $query = "DELETE FROM picking_order WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        $this->execute($stmt);

        $query = "DELETE FROM picking_order_details WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        $this->execute($stmt);

        return "success";
    }

    public function getAllOrders($user_id,$role)
    {   
        switch($role){
            case "admin":
            case "admin-default":
                $query = "SELECT a.*,SUM(c.quantity_order) as total_qty, SUM(c.quantity_shipped) as total_picked
                FROM picking_order a
                LEFT JOIN picking_order_details c ON a.slip_id = c.slip_id
                WHERE a.order_status='prepare' OR a.order_status='repick' GROUP BY a.slip_id";
                break;
            default:
                $query = "SELECT a.*,SUM(c.quantity_order) as total_qty, SUM(c.quantity_shipped) as total_picked
                FROM picking_order a
                LEFT JOIN picking_order_details c ON a.slip_id = c.slip_id
                WHERE a.order_status='prepare' AND a.user_id = '$user_id' OR a.order_status='repick' AND a.user_id = '$user_id' GROUP BY a.slip_id";
        }
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getAllOrdersdetails($slip_id)
    {
        $query = "SELECT a.id,a.slip_id,a.product_id,b.product_code,b.product_description,c.unit_name,a.quantity_order,a.quantity_shipped,a.location,a.order_status,a.stock_id,a.stock_lotno
                FROM picking_order_details a
                LEFT JOIN product b ON a.product_id = b.product_id
                LEFT JOIN unit c ON b.unit_id = c.unit_id WHERE a.slip_id = ?";
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
            case "prepare":
                return $this->getAllOrdersdetails($slip_id);
                break;
            default:
                return false;    
        }        
    }

    public function getAllLots($product_id,$stock_location,$order_status,$slip_id,$stock_lotno)
    {   
        
        // $query = "SELECT stock_lotno FROM stock WHERE product_id = ? AND location_type = 'rak' AND stock_qty > 0 ORDER BY stock_expiration_date ASC";
        // $stmt = $this->prepareQuery($this->conn, $query, "i", array($product_id));
        // $row = $this->fetchRow($stmt);
        // $stock_lotno = $row[0];

        if($order_status == "picked"){

            $query = "SELECT a.stock_id, a.product_id, a.stock_lotno, a.stock_serialno, a.stock_qty, a.stock_expiration_date,a.location_type AS loc,a.picking_order_id,a.from_stock_id,b.rak_name,b.rak_column,b.rak_level,c.location_name,c.location_type,a.location_id
                FROM stock a
                LEFT JOIN rak b ON a.location_id = b.rak_id
                LEFT JOIN cart c ON a.location_id = c.cart_id
                WHERE a.product_id= ? AND a.location_type != 'rak' AND a.picking_order_id = ?
                ORDER BY a.stock_expiration_date ASC,loc DESC LIMIT 10";
            $stmt = $this->prepareQuery($this->conn, $query, "ii", array($product_id,$slip_id));
            return $this->fetchAssoc($stmt);

        }else{

            $query = "SELECT a.stock_id, a.product_id, a.stock_lotno, a.stock_serialno, a.stock_qty, a.stock_expiration_date,a.location_type AS loc,a.picking_order_id,a.from_stock_id,b.rak_name,b.rak_column,b.rak_level,c.location_name,c.location_type,a.location_id
                FROM stock a
                LEFT JOIN rak b ON a.location_id = b.rak_id
                LEFT JOIN cart c ON a.location_id = c.cart_id
                WHERE a.product_id= ? AND a.stock_lotno = ? AND a.picking_order_id = '0' AND a.stock_qty > 0 OR a.product_id= ? AND a.picking_order_id = ? AND a.stock_qty > 0 
                ORDER BY a.stock_expiration_date DESC,loc DESC LIMIT 10";
            $stmt = $this->prepareQuery($this->conn, $query, "isii", array($product_id,$stock_lotno,$product_id,$slip_id));
            return $this->fetchAssoc($stmt);

        }
        
    }

    public function Save_selectedItem($order_details_id,$stock_id,$product_id,$stock_lotno,$stock_serialno,$stock_qty,$stock_expiration_date,$pickingQuantity,$pickingCart,$slip_id,$location_id)
    {
        $date_today = date('Y-m-d');
        $location_type = "cart";
        
        $new_stock_qty = $stock_qty - $pickingQuantity;
        $query = "UPDATE stock SET stock_qty = ? WHERE stock_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "ii", array($new_stock_qty,$stock_id));
        $this->execute($stmt);


        $query = "SELECT stock_qty,stock_id,from_stock_id,stock_lotno,location_id FROM stock WHERE product_id = ? AND location_id = ? AND location_type = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "iis",array($product_id,$pickingCart,$location_type));
        $rowq = $this->fetchRow($stmt);
        $stockqty = $rowq[0];
        $stockid = $rowq[1];
        $from_stock_id = $rowq[2];
        $lotnumero = $rowq[3];
        $loc_id = $rowq[4];
        if($stockid==null || $stockid=="" || $lotnumero!=$stock_lotno || $loc_id!=$location_id){

            $query = "INSERT INTO stock(product_id, location_id, location_type, stock_lotno, stock_serialno, stock_qty, stock_expiration_date, picking_order_id, from_stock_id) VALUES (?,?,?,?,'$stock_serialno',?,?,?,?)";
            $stmt = $this->prepareQuery($this->conn, $query, "iissisii", array($product_id,$pickingCart,$location_type,$stock_lotno ,$pickingQuantity,$stock_expiration_date,$slip_id,$stock_id));
            $this->execute($stmt);

        }else{
            $new_from_stock_id = $from_stock_id.",".$stock_id;
            $stockqty = $stockqty + $pickingQuantity;
            $query = "UPDATE stock SET stock_qty = ?,from_stock_id = ? WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "isi", array($stockqty,$new_from_stock_id,$stockid));
            $this->execute($stmt);

        }

        $query2 = "INSERT INTO stock_logs SET stock_id = '$stock_id', log_type='out', log_qty='$pickingQuantity', log_reference='from rak', log_notes='', log_transaction_date='$date_today', log_transaction_time = '', log_posting_date = '$date_today', end_user = '', approver = '', stock_status = 'pending', comments = '$slip_id'";
        $stmt = $this->prepareQuery($this->conn, $query2);
        $this->execute($stmt);


        $query = "SELECT quantity_shipped,quantity_order FROM picking_order_details WHERE id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($order_details_id));
        $row = $this->fetchRow($stmt);
        $quantity_shipped = $row[0];
        $quantity_order = $row[1];

        $new_qty_shipped = $quantity_shipped + $pickingQuantity;

        $order_status = "picked";
        if($quantity_order > $new_qty_shipped){
            $order_status = "incomplete";
        }
        $query = "UPDATE picking_order_details SET quantity_shipped = ?, order_status = ? WHERE id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "isi", array($new_qty_shipped,$order_status,$order_details_id));
        return $this->execute($stmt);

    }

    public function invoice($slipId, $locationId, $user_name)
    {
        $stockIds = array();

        $query = "UPDATE picking_order SET comments = '', order_status = 'invoice' WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slipId));
        $this->execute($stmt);
        
        $query = "SELECT stock_id FROM picking_order_details WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slipId));
        $stocks = $this->fetchAssoc($stmt);

        if(empty($stocks)) { return true; }

        $stockIds[] = $locationId;

        /* fill stockId array with fetched stocks */
        foreach($stocks as $k=>$v) { $stockIds[] = $stocks[$k]["stock_id"]; }

        /* create placeholders and binding strings */
        $in  = str_repeat('?,', count($stockIds) - 2) . '?';
        $binds  = str_repeat("i", count($stockIds) - 1) . "i";

        // $query = "UPDATE stock SET location_id = ? WHERE stockid IN ($in)";
        $query = "UPDATE stock SET location_id = ? WHERE picking_order_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "ii", array($locationId,$slipId));
        $this->execute($stmt);

        $query = "SELECT slip_no FROM picking_order WHERE slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slipId));
        $row = $this->fetchRow($stmt);
        $slip_no = $row[0];

        $audit_action = 'Send order "'.$slip_no.'" to ccd for invoice';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

    public function Undo_pickedorder($id,$del_stock_id,$stock_lotno,$stock_expiration_date,$stock_quantity,$rak_return_id,$productid,$serial,$undo_qty)
    {
        
        $rak_return_id = strtoupper($rak_return_id);
        $exp_rak_return_id = explode("-", $rak_return_id);

        $rak_return_name = $exp_rak_return_id[0]."-".$exp_rak_return_id[1]."-".$exp_rak_return_id[2];

        $megavariable = 0;
        $date_today = date('Y-m-d');
        $query = "SELECT rak_id FROM rak WHERE rak_name = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "s", array($rak_return_name));
        $row_rak = $this->fetchRow($stmt);
        $fetchRak = $row_rak[0];

        if($fetchRak=="" || $fetchRak==null){

            return "invalid";

        }else{

            $query = "SELECT quantity_shipped,slip_id FROM picking_order_details WHERE id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
            $row = $this->fetchRow($stmt);
            $quantity_shipped = $row[0];
            $slip_id = $row[1];

            $new_stock_qty = $quantity_shipped - $undo_qty;
            
            $order_status = "incomplete";
            if($new_stock_qty==0 || $new_stock_qty==null){
                $order_status = "pending";
            }
            $query = "UPDATE picking_order_details SET quantity_shipped= ? ,stock_id='0',order_status = ? WHERE id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "isi", array($new_stock_qty,$order_status,$id));
            $this->execute($stmt);

            $query = "SELECT stock_id, product_id, location_id, location_type, stock_lotno, stock_serialno, stock_qty, stock_expiration_date FROM stock WHERE location_id = ? AND location_type = 'rak' AND product_id = ? LIMIT 1";
            $stmt = $this->prepareQuery($this->conn, $query, "ii", array($fetchRak,$productid));
            $row = $this->fetchRow($stmt);
            $stockid = $row[0];
            $new_stock_qty = $row[6] + $undo_qty;

            if($stockid=="" || $stockid==null){
                $query = "INSERT INTO stock SET product_id = ?, location_id = ?, location_type = 'rak', stock_lotno = ?, stock_serialno = '$serial', stock_qty = ?, stock_expiration_date = ?, picking_order_id = '0', from_stock_id = '0'";
                $stmt = $this->prepareQuery($this->conn, $query, "iisis", array($productid,$fetchRak,$stock_lotno,$undo_qty,$stock_expiration_date));
                $this->execute($stmt);

                $stockid = $stmt->insert_id;


                $query2 = "INSERT INTO stock_logs SET stock_id = '$stockid', log_type='in', log_qty='$stock_quantity', log_reference='from other location', log_notes='', log_transaction_date='$date_today', log_transaction_time = '', log_posting_date = '$date_today', end_user = '', approver = ''";
                $stmt = $this->prepareQuery($this->conn, $query2);
                $this->execute($stmt);
                
            }else{

                $query = "SELECT SUM(log_qty) as log_quantity FROM stock_logs WHERE stock_id = ? AND log_type='in'";
                $stmt = $this->prepareQuery($this->conn, $query, "i", array($stockid));
                $rowsum = $this->fetchRow($stmt);

                $log_quantity = $rowsum[0];
                $quantity_validity = $new_stock_qty - $log_quantity;
                if($quantity_validity > 0){

                    $query2 = "INSERT INTO stock_logs SET stock_id = '$stockid', log_type='in', log_qty='$quantity_validity', log_reference='from other location', log_notes='', log_transaction_date='$date_today', log_transaction_time = '', log_posting_date = '$date_today', end_user = '', approver = ''";
                    $stmt = $this->prepareQuery($this->conn, $query2);
                    $this->execute($stmt);
                    $megavariable++;
                }

                $query = "UPDATE stock SET stock_qty= ? WHERE stock_id = ?";
                $stmt = $this->prepareQuery($this->conn, $query, "ii", array($new_stock_qty,$stockid));
                $this->execute($stmt);
            }

            

            $query = "SELECT stock_qty,from_stock_id FROM stock WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($del_stock_id));
            $rowq = $this->fetchRow($stmt);
            $stock_qty = $rowq[0];
            $from_stock_id = $rowq[1];
            $log_type = "out";
            $stock_status = "pending";

            $stock_validity = $stock_qty - $undo_qty;
            if($stock_validity==0){

                $query = "DELETE FROM stock WHERE stock_id = ?";
                $stmt = $this->prepareQuery($this->conn, $query, "i", array($del_stock_id));
                $this->execute($stmt);
                    
                    if($megavariable==0){
                        $query = "DELETE FROM stock_logs WHERE stock_id = ? AND log_type = ? AND stock_status = ? AND comments = ?";
                        $stmt = $this->prepareQuery($this->conn, $query, "issi", array($from_stock_id,$log_type,$stock_status,$slip_id));
                        $this->execute($stmt);
                    }else{
                        $query = "UPDATE stock_logs SET stock_status='' ,comments= '' WHERE stock_id = ? AND log_type = ? AND stock_status = ? AND comments = ?";
                        $stmt = $this->prepareQuery($this->conn, $query, "issi", array($from_stock_id,$log_type,$stock_status,$slip_id));
                        $this->execute($stmt);
                    }
                    

            }else{

                $query = "UPDATE stock SET stock_qty= ? WHERE stock_id = ?";
                $stmt = $this->prepareQuery($this->conn, $query, "ii", array($stock_validity,$del_stock_id));
                $this->execute($stmt);

                $query = "UPDATE stock_logs SET log_qty= ? WHERE stock_id = ? AND log_type = ? AND stock_status = ? AND comments = ?";
                $stmt = $this->prepareQuery($this->conn, $query, "iissi", array($stock_validity,$from_stock_id,$log_type,$stock_status,$slip_id));
                $this->execute($stmt);

            }


            

            return "success";
        }

        

    }

    public function countPickedOrder($slip_id)
    {
        $order_status = "pending";
        $query = "SELECT slip_id FROM picking_order_details WHERE slip_id = ? AND order_status = ?";
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

    public function finishedOrders() 
    {
        
        $order_status = "finished";
        $query = "SELECT slip_id, slip_no, slip_order_date, bill_to, ship_to, po_no, ship_date, invoice_no, order_status FROM picking_order WHERE order_status = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "s", array($order_status));

        return $this->fetchAssoc($stmt);

    }

    
    public function orderAnalytics() 
    {

        $fp = $fi = $p = $fd = 0;
        $query = "SELECT COUNT(slip_id) FROM picking_order WHERE order_status = 'prepare'";
        $stmt = $this->prepareQuery($this->conn, $query);
        $prepare = $this->fetchRow($stmt);

        $query = "SELECT COUNT(slip_id) FROM picking_order WHERE order_status = 'invoice'";
        $stmt = $this->prepareQuery($this->conn, $query);
        $invoice = $this->fetchRow($stmt);

        $query = "SELECT COUNT(slip_id) FROM picking_order WHERE order_status = 'pack'";
        $stmt = $this->prepareQuery($this->conn, $query);
        $pack = $this->fetchRow($stmt);

        $query = "SELECT COUNT(slip_id) FROM picking_order WHERE order_status = 'deliver'";
        $stmt = $this->prepareQuery($this->conn, $query);
        $deliver = $this->fetchRow($stmt);

        if(!empty($prepare)) { $fp = (int)($prepare[0]) ?: 0; }
        if(!empty($prepare)) { $fi = (int)($invoice[0]) ?: 0; }
        if(!empty($pack)) { $p = (int)($pack[0]) ?: 0; }
        if(!empty($invoice)) { $fd = (int)($deliver[0]) ?: 0; }

        return array(array($fp),array($fi),array($p),array($fd));

    }

    public function allFinishedOrdersdetails($slip_id)
    {

        $query = "SELECT a.quantity_order, a.quantity_shipped, a.location, a.stock_lotno, b.category_id, b.unit_id, b.product_code, b.product_description, b.product_image 
            FROM picking_order_details a 
            LEFT JOIN product b ON a.product_id = b.product_id
            WHERE a.slip_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($slip_id));
        
        return $this->fetchAssoc($stmt);

    }

}