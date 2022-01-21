<?php

class AddOrder extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function addOrder($slipno,$sliporder_date,$billto,$shipto,$reference,$pono,$customer_address,$salesperson,$shipvia,$shipdate,$user_name)
    {   


        $usertype = "picker";
        $qry = "SELECT user_id FROM user_account WHERE user_usertype = ?";
        $stmt = $this->prepareQuery($this->conn, $qry, "s", array($usertype));
        $orders = $this->fetchAssoc($stmt);
        $uid = array();
        foreach($orders as $k=>$v) {
          $user_id = $v["user_id"]; 
          $uid[] = $user_id;
        }
        $noofuser = count($uid);

        $order_status = "prepare";
        $query = "SELECT user_id FROM picking_order WHERE order_status = ? ORDER BY slip_id DESC LIMIT 1";
        $stmt = $this->prepareQuery($this->conn, $query, "s", array($order_status));
        $row = $this->fetchRow($stmt);
        $last_userid = $row[0]; 

        $i=0;
        $Index=0;
        for($i=0;$i<$noofuser;$i++){
            $userId = $uid[$i];
            if($userId==$last_userid){
                $Index = $i;
                if($Index+1==$noofuser){
                    $Index = 0;
                }else{
                    $Index += 1;
                }
            }
        }
        $final_Userid = $uid[$Index];
        if($last_userid==0 || $last_userid==null || $last_userid==""){
            $final_Userid = $uid[0];
        }

        $shipdate = date_format(date_create($shipdate), "Y-m-d");
        $sliporder_date = date_format(date_create($sliporder_date), "Y-m-d");

        $shipvia = 1;
        $query = "INSERT INTO picking_order SET slip_no = ?, slip_order_date = ?, bill_to = ?, ship_to = ?, reference = ?, po_no = ?, customer_address = ?, sales_person = ?, truck_id = ?, ship_date = ?, comments = '', approved_by = '', invoice_billed_by = '', invoice_no = '', order_status = 'prepare', user_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "ssssssssisi", array($slipno,$sliporder_date,$billto,$shipto,$reference,$pono,$customer_address,$salesperson,$shipvia,$shipdate,$final_Userid));
        $this->execute($stmt);

        $lastid = $stmt->insert_id;

        $audit_action = 'Import new order with slip no "'.$slipno.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        $this->execute($stmt);

        return $lastid;

    }

    public function addOrderDetails($slip_id,$product_code,$quantity_ordered,$location,$stock_lotno)
    {   


        $query = "SELECT product_id FROM product WHERE product_code = ? LIMIT 1";
        $stmt = $this->prepareQuery($this->conn, $query, "s", array($product_code));
        $row = $this->fetchRow($stmt);
        $product_id = $row[0];

        $query = "INSERT INTO picking_order_details SET slip_id = ?, product_id = ?, quantity_order = ?, quantity_shipped = '0', location = ?, stock_id = '0', order_status = 'pending', checking_status = '', stock_lotno = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "iiiss", array($slip_id,$product_id,$quantity_ordered,$location,$stock_lotno));
        return $this->execute($stmt);

    }



}