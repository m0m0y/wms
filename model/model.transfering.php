<?php

class Transfering extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllTransfering()
    {
        $query = "SELECT a.*,b.product_id,b.location_id,bb.rak_name AS b_rakname,bb.rak_column AS b_rakcolumn,bb.rak_level AS b_raklevel,c.product_code,c.product_description,d.rak_name,d.rak_column,d.rak_level 
                FROM transfer_item a
                LEFT JOIN stock b ON a.stock_id = b.stock_id
                LEFT JOIN product c ON b.product_id = c.product_id
                LEFT JOIN rak d ON a.rak_id = d.rak_id
                LEFT JOIN rak bb ON b.location_id = bb.rak_id
                WHERE a.transfer_status = 'Approve' OR a.transfer_status = 'Picked'";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getAllTransferingDetails($transfer_id)
    {
        $query = "SELECT a.*,b.product_id,b.location_id,bb.rak_name AS b_rakname,bb.rak_column AS b_rakcolumn,bb.rak_level AS b_raklevel,c.product_code,c.product_description,d.rak_name,d.rak_column,d.rak_level 
                FROM transfer_item a
                LEFT JOIN stock b ON a.stock_id = b.stock_id
                LEFT JOIN product c ON b.product_id = c.product_id
                LEFT JOIN rak d ON a.rak_id = d.rak_id
                LEFT JOIN rak bb ON b.location_id = bb.rak_id  WHERE a.id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($transfer_id));
        return $this->fetchAssoc($stmt);
    }

    public function getOrderStatus($transfer_id)
    {
        $query = "SELECT transfer_status FROM transfer_item WHERE id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($transfer_id));
        
        $data = $this->fetchRow($stmt);
        if(!$data) { return false; }

        switch($data[0]) {
            case "Pending":
            case "Disapprove":
            case "Picked":
                return $this->getAllTransferingDetails($transfer_id);
                break;
            case "Approve":
                return $this->getAllTransferingDetails($transfer_id);
                break;
            default:
                return false;    
        }        
    }

    public function getAllLots($stock_id, $location_type,$transfer_status,$rak_id)
    {   
        $query = "SELECT a.stock_id, a.product_id, a.stock_lotno, a.stock_serialno, a.stock_qty, a.stock_expiration_date,a.location_type AS loc,a.picking_order_id,a.from_stock_id,b.rak_name,b.rak_column,b.rak_level,c.location_name,c.location_type
            FROM stock a
            LEFT JOIN rak b ON a.location_id = b.rak_id
            LEFT JOIN cart c ON a.location_id = c.cart_id
            WHERE stock_id = ?
            ORDER BY a.stock_expiration_date ASC,loc DESC LIMIT 10";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($stock_id));
        return $this->fetchAssoc($stmt);
    }

    public function Save_selectedItem($order_details_id,$stock_id,$product_id,$stock_lotno,$stock_serialno,$stock_qty,$stock_expiration_date,$pickingQuantity,$pickingRak,$transfer_id)
    {
        $date_today = date('Y-m-d');
        $location_type = "rak";
        
        $new_stock_qty = $stock_qty - $pickingQuantity;
        
        $query = "INSERT INTO stock(product_id, location_id, location_type, stock_lotno, stock_serialno, stock_qty, stock_expiration_date, picking_order_id, from_stock_id) VALUES (?,?,?,?,'$stock_serialno',?,?,'0','0')";
        $stmt = $this->prepareQuery($this->conn, $query, "iissis", array($product_id,$pickingRak,$location_type,$stock_lotno ,$pickingQuantity,$stock_expiration_date));
        $this->execute($stmt);
        $lastid = $stmt->insert_id;

        $query2 = "INSERT INTO stock_logs SET stock_id = '$stock_id', log_type='out', log_qty='$pickingQuantity', log_reference='from rak', log_notes='', log_transaction_date='$date_today', log_transaction_time = '', log_posting_date = '$date_today', end_user = '', approver = ''";
        $stmt = $this->prepareQuery($this->conn, $query2);
        $this->execute($stmt);

        $query2 = "INSERT INTO stock_logs SET stock_id = '$lastid', log_type='in', log_qty='$pickingQuantity', log_reference='from rak', log_notes='', log_transaction_date='$date_today', log_transaction_time = '', log_posting_date = '$date_today', end_user = '', approver = ''";
        $stmt = $this->prepareQuery($this->conn, $query2);
        $this->execute($stmt);

        // if($new_stock_qty==0){

        //     $query = "DELETE FROM stock WHERE stock_id = ?";
        //     $stmt = $this->prepareQuery($this->conn, $query, "i", array($stock_id));
        //     $this->execute($stmt);

        //     $query = "DELETE FROM stock_logs WHERE stock_id = ?";
        //     $stmt = $this->prepareQuery($this->conn, $query, "i", array($stock_id));
        //     $this->execute($stmt);

        // }else{

            $query = "UPDATE stock SET stock_qty = ? WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "ii", array($new_stock_qty,$stock_id));
            $this->execute($stmt);

        // }

        $t_status = "Picked";
        $query = "UPDATE transfer_item SET transfer_status = ?, new_stock_id = ? WHERE id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "sii", array($t_status,$lastid,$transfer_id));
        return $this->execute($stmt);

    }

    public function Undo_pickedorder($id,$del_stock_id,$stock_lotno,$stock_expiration_date,$stock_quantity,$rak_return_id,$productid,$serial,$new_stock_id,$transfer_id)
    {
        
        $date_today = date('Y-m-d');

            $query = "SELECT stock_id, product_id, location_id, location_type, stock_lotno, stock_serialno, stock_qty, stock_expiration_date FROM stock WHERE stock_id = ? LIMIT 1";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($rak_return_id));
            $row = $this->fetchRow($stmt);
            $stockid = $row[0];
            $new_stock_qty = $row[6] + $stock_quantity;

            if($stockid=="" || $stockid==null){
                $query = "INSERT INTO stock SET product_id = ?, location_id = ?, location_type = 'rak', stock_lotno = ?, stock_serialno = '$serial', stock_qty = ?, stock_expiration_date = ?, picking_order_id = '0', from_stock_id = '0'";
                $stmt = $this->prepareQuery($this->conn, $query, "iisis", array($productid,$rak_return_id,$stock_lotno,$stock_quantity,$stock_expiration_date));
                $this->execute($stmt);

                $stockid = $stmt->insert_id;
                
            }else{
                $query = "UPDATE stock SET stock_qty= ? WHERE stock_id = ?";
                $stmt = $this->prepareQuery($this->conn, $query, "ii", array($new_stock_qty,$stockid));
                $this->execute($stmt);
            }

            $query2 = "INSERT INTO stock_logs SET stock_id = '$stockid', log_type='in', log_qty='$stock_quantity', log_reference='from other location', log_notes='', log_transaction_date='$date_today', log_transaction_time = '', log_posting_date = '$date_today', end_user = '', approver = ''";
            $stmt = $this->prepareQuery($this->conn, $query2);
            $this->execute($stmt);

            

            $query = "DELETE FROM stock WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($new_stock_id));
            $this->execute($stmt);

            $query = "DELETE FROM stock_logs WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($new_stock_id));
            $this->execute($stmt);

            $query = "UPDATE transfer_item SET transfer_status= 'Approve',new_stock_id='0' WHERE id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($transfer_id));
            $this->execute($stmt);
            return "success";
            // return $stockid;
    }

    public function end_Transaction($transfer_id){

            $query = "UPDATE transfer_item SET transfer_status= 'Finished' WHERE id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($transfer_id));
            $this->execute($stmt);

            return "success";
    }


    public function validateStorage($id,$type)
    {
        $query = "SELECT rak_id FROM rak WHERE rak_id = ? LIMIT 1";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $result = $this->fetchCount($stmt);
        return ($result) ? true : false;
    }

    public function addTransfer($stock_id,$rak_id,$quantity_stock,$userid)
    {
        $date_request = date('Y-m-d');
        $query = "INSERT INTO transfer_item(stock_id, stock_qty_moving, rak_id, transfer_status, requested_by, date_request) VALUES(?,?,?,?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "iiisis", array($stock_id,$quantity_stock,$rak_id,"Pending",$userid,$date_request));
        return $this->execute($stmt);
    }

    public function getMovable($lot) {
        $query = "SELECT s.stock_id, s.stock_qty, r.* FROM stock s LEFT JOIN rak r ON s.location_id = r.rak_id WHERE s.location_type = 'rak' AND s.stock_lotno = ? AND s.stock_qty != 0 GROUP BY s.stock_id";
        $stmt = $this->prepareQuery($this->conn, $query, "s", array($lot));
        return $this->fetchAssoc($stmt);
    }

    public function getLocations($stockid) {

        $query = "SELECT location_id FROM stock WHERE stock_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($stockid));
        $row = $this->fetchRow($stmt);
        $rak_id = $row[0];

        $query = "SELECT * FROM rak WHERE rak_id != ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($rak_id));
        return $this->fetchAssoc($stmt);
    }

}