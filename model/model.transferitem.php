<?php

class Transfer extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllTransfer()
    {
        $query = "SELECT a.*,b.product_id,b.location_id,bb.rak_name AS b_rakname,bb.rak_column AS b_rakcolumn,bb.rak_level AS b_raklevel,c.product_code,c.product_description,d.rak_name,d.rak_column,d.rak_level, e.user_fullname FROM transfer_item a
                LEFT JOIN stock b ON a.stock_id = b.stock_id
                LEFT JOIN product c ON b.product_id = c.product_id
                LEFT JOIN rak d ON a.rak_id = d.rak_id
                LEFT JOIN rak bb ON b.location_id = bb.rak_id
                LEFT JOIN user_account e ON a.requested_by = e.user_id
                ORDER BY a.transfer_status DESC";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getStockIds($lot)
    {
        $query = "SELECT s.stock_id, s.location_id, s.stock_lotno, s.stock_serialno, s.stock_qty, r.* FROM stock s
        LEFT JOIN rak r ON s.location_id = r.rak_id  WHERE s.stock_lotno = ? AND s.location_type = 'rak' GROUP BY s.stock_id";
        $stmt = $this->prepareQuery($this->conn, $query, "s", array($lot));
        return $this->fetchAssoc($stmt);
    }

    public function reviewTransfer($id, $status)
    {
        if($status=="Undo"){

            $status = "Pending";
            $clear_id = 0;
            $query = "UPDATE transfer_item SET rak_id = ?, transfer_status = ?, requested_by = ? WHERE id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "isii", array($clear_id, $status, $clear_id, $id));
            return $this->execute($stmt);

        }else{

            $query = "UPDATE transfer_item SET transfer_status = ? WHERE id = ?";
            $stmt = $this->prepareQuery($this->conn, $query, "si", array($status, $id));
            return $this->execute($stmt);
        }
        
    }

    public function delete_Transfer($id)
    {

        $query = "DELETE FROM transfer_item WHERE id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        return $this->execute($stmt);
    }

    public function getQty($id)
    {
        $query = "SELECT stock_id, stock_qty_moving FROM transfer_item WHERE id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        return $this->fetchRow($stmt);
    }

    public function updateQty($id, $val)
    {
        $query = "UPDATE transfer_item SET stock_qty_moving = ? WHERE id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "ii", array($val, $id));
        return $this->execute($stmt);
    }

    public function addTransfer($stock_id,$rak_id,$quantity_stock,$userid)
    {
        $date_request = date('Y-m-d');
        $query = "INSERT INTO transfer_item(stock_id, stock_qty_moving, rak_id, transfer_status, requested_by, date_request) VALUES(?,?,?,?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "iiisis", array($stock_id,$quantity_stock,$rak_id,"Approve",$userid,$date_request));
        return $this->execute($stmt);
    }


}