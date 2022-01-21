<?php

class Dashboard extends DBHandler {

    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllPicking()
    {
        $query = "SELECT a.*,SUM(c.quantity_order) as total_qty, SUM(c.quantity_shipped) as total_picked, u.user_username, u.user_fullname
                FROM picking_order a
                LEFT JOIN picking_order_details c ON a.slip_id = c.slip_id 
                LEFT JOIN user_account u ON a.user_id = u.user_id 
                WHERE a.order_status='prepare' OR a.order_status='repick' GROUP BY a.slip_id";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getAllInvoice()
    {
        $query = "SELECT a.*,SUM(c.quantity_order) as total_qty, SUM(c.quantity_shipped) as total_picked
                FROM picking_order a
                LEFT JOIN picking_order_details c ON a.slip_id = c.slip_id
                WHERE a.order_status='invoice' GROUP BY a.slip_id";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getAllPacking()
    {
        $query = "SELECT a.*,SUM(distinct c.quantity_order) as total_qty, SUM(distinct s.stock_qty) as total_picked
                FROM picking_order a
                LEFT JOIN picking_order_details c ON a.slip_id = c.slip_id
                LEFT JOIN stock s ON (a.slip_id = s.picking_order_id AND s.box_number <> '')
                WHERE a.order_status='pack' GROUP BY a.slip_id";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getAllShipping()
    {
        $query = "SELECT a.*,SUM(c.quantity_order) as total_qty, SUM(c.quantity_shipped) as total_picked
                FROM picking_order a
                LEFT JOIN picking_order_details c ON a.slip_id = c.slip_id
                WHERE a.order_status='ship' OR a.order_status='deliver' GROUP BY a.slip_id";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getAllChecking()
    {
        $query = "SELECT a.*,SUM(distinct c.quantity_order) as total_qty, SUM(distinct d.quantity_shipped) as total_picked
                FROM picking_order a
                LEFT JOIN picking_order_details c ON a.slip_id = c.slip_id 
                LEFT JOIN picking_order_details d ON (a.slip_id = d.slip_id AND d.checking_status = 'approved')
                WHERE a.order_status='checking' GROUP BY a.slip_id";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

}