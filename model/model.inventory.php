<?php

class Inventory extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllProducts()
    {

        $query = "SELECT a.product_id, SUM(b.stock_qty) as quantity, a.product_code, a.product_description, a.product_expiration,a.product_type, u.unit_name
                FROM product a
                LEFT JOIN stock b ON a.product_id = b.product_id
                LEFT JOIN unit u ON a.unit_id = u.unit_id
                GROUP BY a.product_id";

        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getProductdetails($product_id)
    {
        $query = "SELECT a.stock_id,a.product_id,a.location_id,a.location_type,a.stock_lotno,a.stock_serialno,a.stock_expiration_date,a.stock_qty,b.product_code,c.rak_name,c.rak_column,c.rak_level,d.location_name,u.unit_name,e.log_reference,e.log_notes,e.log_transaction_date
            FROM stock a
            LEFT JOIN product b ON a.product_id=b.product_id
            LEFT JOIN rak c ON a.location_id=c.rak_id
            LEFT JOIN cart d ON a.location_id=d.cart_id
            LEFT JOIN unit u ON b.unit_id = u.unit_id
            LEFT JOIN stock_logs e ON a.stock_id = e.stock_id
            WHERE a.product_id= ?
            GROUP BY a.stock_id";
        $stmt = $this->prepareQuery($this->conn, $query, "i",array($product_id));
        return $this->fetchAssoc($stmt);

    }

    public function getStockcard($stock_id)
    {
        $query = "SELECT a.*,b.stock_lotno,b.stock_serialno,b.stock_expiration_date,b.stock_qty FROM stock_logs a
                  LEFT JOIN stock b ON a.stock_id = b.stock_id 
                  WHERE a.stock_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i",array($stock_id));
        return $this->fetchAssoc($stmt);

    }

    public function validateStock($stock_id){

        $query = "SELECT stock_id FROM stock WHERE from_stock_id = ? LIMIT 1";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($stock_id));
        $row = $this->fetchRow($stmt);
        $stockid = $row[0];   

        if($stockid==null || $stockid=="" || $stockid==0){

            
            $query = "SELECT count(log_id) as ct FROM stock_logs WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($stock_id));
            $row = $this->fetchRow($stmt);
            $countId = $row[0];  
            if($countId > 1){
                return "found";
            }else{
                return "notfound";
            }

        }else{
            return "found";
        }     

    }

    public function getProductAnalytics($product_id = 0)
    {
        $rk = $rc = $rt = 0;
        if($product_id) {
            
            $query = "SELECT SUM(stock_qty) FROM stock WHERE product_id = ? AND location_type = 'rak'";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($product_id));
            $rak = $this->fetchRow($stmt);
            
            $query = "SELECT SUM(stock_qty) FROM stock WHERE product_id = ? AND location_type = 'cart'";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($product_id));
            $cart = $this->fetchRow($stmt);
            
            $query = "SELECT SUM(stock_qty) FROM stock WHERE product_id = ? AND location_type = 'truck'";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($product_id));
            $truck = $this->fetchRow($stmt);

        } else {
            
            $query = "SELECT SUM(stock_qty) FROM stock WHERE location_type = 'rak'";
            $stmt = $this->prepareQuery($this->conn, $query);
            $rak = $this->fetchRow($stmt);
            
            $query = "SELECT SUM(stock_qty) FROM stock WHERE location_type = 'cart'";
            $stmt = $this->prepareQuery($this->conn, $query);
            $cart = $this->fetchRow($stmt);
            
            $query = "SELECT SUM(stock_qty) FROM stock WHERE location_type = 'truck'";
            $stmt = $this->prepareQuery($this->conn, $query);
            $truck = $this->fetchRow($stmt);
        }

        if(!empty($rak)) { $rk = (int)($rak[0]) ?: 0; }
        if(!empty($cart)) { $rc = (int)($cart[0]) ?: 0; }
        if(!empty($truck)) { $rt = (int)($truck[0]) ?: 0; }

        $total = (int)$rk + (int)$rc + (int)$rt;
        $rkp = ($rk) ? number_format(($rk/$total) * 100, "1", ".", ",") : 0;
        $rcp = ($rc) ? number_format(($rc/$total) * 100, "1", ".", ",") : 0;
        $rtp = ($rt) ? number_format(($rt/$total) * 100, "1", ".", ",") : 0;
        
        return array(array($rc,$rcp),array($rk,$rkp),array($rt,$rtp));
    }

    public function addNewStock($product_id,$stock_qty,$rak_id,$stock_lotno,$stock_serialno,$reference,$notes,$stock_expiration_date,$transaction_date,$user_name)
    {   

        $date_posted = date('Y-m-d');
        $location_type = "rak";
        $query = "SELECT stock_id, stock_qty FROM stock WHERE product_id = '$product_id' AND location_id = ? AND location_type = 'rak' AND stock_lotno = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "is", array($rak_id,$stock_lotno));
        $row = $this->fetchRow($stmt);
        $stockid = $row[0];
        $new_stock_qty = $row[1] + $stock_qty;

        if($stockid=="" || $stockid==null){

            
            $query = "INSERT INTO stock SET product_id = '$product_id', location_id = '$rak_id', location_type = '$location_type', stock_lotno = '$stock_lotno', stock_serialno = '$stock_serialno', stock_qty = '$stock_qty', stock_expiration_date = '$stock_expiration_date', picking_order_id = '0', from_stock_id = '0'";
            $stmt = $this->prepareQuery($this->conn, $query);
            $this->execute($stmt);

            $stockid = $stmt->insert_id;

        }else{

            $query = "UPDATE stock SET stock_qty = '$new_stock_qty' WHERE stock_id = '$stockid'";
            $stmt = $this->prepareQuery($this->conn, $query);
            $this->execute($stmt);

        }

            $query2 = "INSERT INTO stock_logs SET stock_id = '$stockid', log_type='in', log_qty='$stock_qty', log_reference='$reference', log_notes='$notes', log_transaction_date='$transaction_date', log_transaction_time = '', log_posting_date = '$date_posted', end_user = '', approver = ''";
            $stmt = $this->prepareQuery($this->conn, $query2);
            $this->execute($stmt);

            $query = "SELECT product_code FROM product WHERE product_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($product_id));
            $row = $this->fetchRow($stmt);
            $p_code = $row[0];

            $audit_action = 'Added new stock in "'.$p_code.'"';
            $audit_date = date('F d, Y h:i:s');
            $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
            $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
            return $this->execute($stmt);
        
    }

    public function deleteStock($stock_id,$product_id,$user_name)
    {
        $query = "SELECT product_code FROM product WHERE product_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($product_id));
        $row = $this->fetchRow($stmt);
        $p_code = $row[0];

        $query = "DELETE FROM stock WHERE stock_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($stock_id));
        $this->execute($stmt);

        $query = "DELETE FROM stock_logs WHERE stock_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($stock_id));
        $this->execute($stmt);

        $audit_action = 'Delete stock on product "'.$p_code.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

    public function updateStock($stock_id,$product_id,$stock_qty,$rak_id,$stock_lotno,$stock_serialno,$reference,$notes,$stock_expiration_date,$transaction_date,$user_name)
    {
        $notes = str_replace("&#39;", "", $notes);

        $query = "UPDATE stock SET product_id = ?,stock_qty = ?, location_id = ?, stock_lotno = ?, stock_serialno = '$stock_serialno', stock_expiration_date = '$stock_expiration_date' WHERE stock_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "isisi", array($product_id,$stock_qty,$rak_id,$stock_lotno,$stock_id));
        $this->execute($stmt);

        $log_type = 'in';
        $query = "UPDATE stock_logs SET log_qty = ?, log_reference = '$reference', log_notes = '$notes', log_transaction_date = ? WHERE stock_id = ? AND log_type = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "isis", array($stock_qty,$transaction_date,$stock_id,$log_type));
        $this->execute($stmt);

        $query = "SELECT product_code FROM product WHERE product_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($product_id));
        $row = $this->fetchRow($stmt);
        $p_code = $row[0];

        $audit_action = 'Update the stock of "'.$p_code.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

    public function addStocks($product_code,$product_description,$product_type,$uom,$quantity_ordered,$stock_lotno,$stock_serialno,$stock_expiration_date)
    {

        $product_code_replace = str_replace(array("'", "&quot;"), "", htmlspecialchars($product_code));
        $product_desc_replace = str_replace(array("'", "&quot;"), "", htmlspecialchars($product_description));
        $product_type_lowerCase = strtolower($product_type);

        if($stock_expiration_date!=null || $stock_expiration_date!=""){
            $var = preg_split("#/#", $stock_expiration_date);
            if($var[0] <=9){
                $var[0] = '0'.$var[0];
            }
            if($var[1] <=9){
                $var[1] = '0'.$var[1];
            }
            $stock_expiration_date = $var[2].'-'.$var[0].'-'.$var[1];
        }
      
        $exp = "yes";
        if($stock_expiration_date==null || $stock_expiration_date=="" || $stock_expiration_date=="0000-00-00"){
            $exp = "no";
        }
        
        $query = "SELECT unit_id FROM unit WHERE unit_name = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "s", array($uom));
        $row = $this->fetchRow($stmt);
        $unit_id = $row[0];

        if($unit_id =="" || $unit_id ==null){

            $query = "INSERT INTO unit SET unit_name = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "s", array($uom));
            $this->execute($stmt);
            $unit_id = $stmt->insert_id;

        }

        $query = "SELECT product_id FROM product WHERE product_code = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "s", array($product_code_replace));
        $row = $this->fetchRow($stmt);
        $product_id = $row[0];

        if($product_id =="" || $product_id ==null){

            $query = "INSERT INTO product SET unit_id = ?, product_type = ?, product_code = ?, product_description = ?, product_expiration = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "issss", array($unit_id,$product_type_lowerCase,$product_code_replace,$product_desc_replace,$exp));
            $this->execute($stmt);
            $product_id = $stmt->insert_id;
            
        }
        
        $location_id = "15";
        $location_type = "cart";
        $query = "SELECT stock_id,from_stock_id FROM stock WHERE product_id = ? AND location_id = ? AND location_type = ? AND stock_lotno = ? AND stock_qty = ? AND stock_expiration_date = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "iissis", array($product_id,$location_id,$location_type,$stock_lotno,$quantity_ordered,$stock_expiration_date));
        $rowq = $this->fetchRow($stmt);
        $stock_id = $rowq[0];
        $from_stock_id = $rowq[1];

            $log_type = "in";
            $log_reference = "returned order";
            $date_today = date('Y-m-d');

        if($stock_id!="" || $stock_id!=null){

            $query = "SELECT stock_qty FROM stock WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($from_stock_id));
            $roww = $this->fetchRow($stmt);
            $stockqty = $roww[0];
            $new_stock = $stockqty + $quantity_ordered;
            
            $query = "UPDATE stock SET stock_qty = ? WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "ii", array($new_stock,$from_stock_id));
            $this->execute($stmt);

            $query = "INSERT INTO stock_logs SET stock_id = ?, log_type = ?, log_qty = ?, log_reference = ?, log_transaction_date = ?, log_posting_date = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "isisss", array($from_stock_id,$log_type,$quantity_ordered,$log_reference,$date_today,$date_today));
            $this->execute($stmt);

            $query = "DELETE FROM stock WHERE stock_id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "i", array($stock_id));
            $this->execute($stmt);

        }else{

            $log_reference = "";

            $query = "INSERT INTO stock SET product_id = ?, location_id = '0', location_type = 'rak', stock_lotno = ?, stock_serialno = '$stock_serialno', stock_qty = ?, stock_expiration_date = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "isis", array($product_id,$stock_lotno,$quantity_ordered,$stock_expiration_date));
            $this->execute($stmt);
            $stock_id = $stmt->insert_id;

            $query = "INSERT INTO stock_logs SET stock_id = ?, log_type = ?, log_qty = ?, log_reference = ?, log_transaction_date = ?, log_posting_date = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "isisss", array($stock_id,$log_type,$quantity_ordered,$log_reference,$date_today,$date_today));
            $this->execute($stmt);

            $transfer_status = "Pending";
            $query = "INSERT INTO transfer_item SET stock_id = ?, stock_qty_moving = ?, transfer_status = ?, date_request = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "iiss", array($stock_id,$quantity_ordered,$transfer_status,$date_today));
            $this->execute($stmt);

        }
        
        return "success";
     
    }
 


}