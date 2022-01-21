<?php

class Receiving extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function addReport($company_name, $origin, $type, $kind, $control_no, $remarks, $disposition, $reference, $delivery, $total_weight)
    {
        $query = "INSERT INTO receiving_report(company_name, origin, type, kind, control_no, remarks, disposition, reference, delivery,expected_weight) VALUES(?,?,?,?,?,?,?,?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "ssssisssss", array($company_name, $origin, $type, $kind, $control_no, $remarks, $disposition, $reference, $delivery, $total_weight));
        $this->execute($stmt);

        return $stmt->insert_id;
    }

    public function addItem($report_id, $item_code, $item_lot, $item_description, $item_expiry_month, $item_expiry_year, $item_unit)
    {
        $query = "INSERT INTO receiving_report_item(report_id, item_code, item_lot, item_description, item_expiry_month, item_expiry_year, item_unit) VALUES(?,?,?,?,?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "issssis", array($report_id, $item_code, $item_lot, $item_description, $item_expiry_month, $item_expiry_year, $item_unit));
        return $this->execute($stmt);
    }

    // public function getAllReports($inactive = false)
    // {
    //     $query = "SELECT r.* FROM receiving_report r WHERE statuss = ''";
    //     if($inactive) {
    //         $query .= " WHERE report_status = 0 AND statuss = ''";
    //     }
    //     $stmt = $this->prepareQuery($this->conn, $query);
    //     return $this->fetchAssoc($stmt);
    // }

    public function getAllReports($report_status)
    {   
        if($report_status=="Pending"){
            $report_status = "";
        }
        $query = "SELECT report_id, company_name, origin, type, kind, no_package, control_no, remarks, disposition, reference, delivery, date_added, report_status, expected_weight, statuss FROM receiving_report WHERE statuss = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "s", array($report_status));
        return $this->fetchAssoc($stmt);
    }

    public function UpdatsStatusRR($report_id,$statuss)
    {
        $query = "UPDATE receiving_report SET statuss= ? WHERE report_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($statuss,$report_id));
        return $this->execute($stmt); 
    }

    public function getAllReportItems($report_id)
    {
        $query = "SELECT * FROM receiving_report_item WHERE report_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($report_id));
        return $this->fetchAssoc($stmt);    
    }

    
    public function getReport($report_id)
    {
        $query = "SELECT * FROM receiving_report WHERE report_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($report_id));
        $report = $this->fetchAssoc($stmt);    
        $query = "SELECT * FROM receiving_report_item WHERE report_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($report_id));
        $items = $this->fetchAssoc($stmt);
        $report[0]["items"] = $items;
        return $report;
    }

    public function deleteReport($report_id)
    {
        $query = "DELETE FROM receiving_report WHERE report_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($report_id));
        $this->execute($stmt); 
        
        $query = "DELETE FROM receiving_report_item WHERE report_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($report_id));
        $this->execute($stmt); 

        return true;
    }

    public function updateQty($report_id, $qty) {
        $query = "UPDATE receiving_report_item SET item_received = ? WHERE item_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "ii", array($qty, $report_id));
        return $this->execute($stmt);
    }
    
    public function finishReceiving($report_id,$user_fullname) {
        $query = "UPDATE receiving_report SET report_status = 1 WHERE report_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($report_id));
        $this->execute($stmt);   

        $query = "UPDATE receiving_report_item SET user_fullname = ? WHERE report_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "si", array($user_fullname,$report_id));
        return $this->execute($stmt);    
    }

    public function reReceiving($report_id) {
        $query = "UPDATE receiving_report SET report_status = 0 WHERE report_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($report_id));
        return $this->execute($stmt);    
    }

    public function selectItem($item_code){
        $query = "SELECT unit_id,product_description FROM product WHERE product_code = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "s", array($item_code));
        $row = $this->fetchRow($stmt);
        $unit_id = $row[0];
        $product_description = $row[1];

        return array("unit_id"=>$unit_id,"product_description"=>$product_description);
    }



}