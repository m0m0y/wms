<?php

class Detrack extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllOrders()
    {
        $query = "SELECT slip_id, slip_no, slip_order_date, bill_to, ship_to, reference, po_no, customer_address, sales_person, truck_id, ship_date, comments, approved_by, invoice_billed_by, invoice_no, order_status, user_id, do_no, tracking_status, assign_to, time_delivered, received_by, deliver_img, delivery_location FROM picking_order WHERE order_status = 'deliver'";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getAllusers(){
        $query = "SELECT a.location_id, a.user_id, a.location, a.timestamp, b.user_fullname FROM user_location a LEFT JOIN user_account b ON a.user_id=b.user_id";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getAllOrdersnew()
    {
        $query = "SELECT a.*,b.user_fullname FROM picking_order a LEFT JOIN user_account b ON a.assign_to = b.user_id WHERE a.order_status = 'deliver'";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getUserOrders($id)
    {
        $query = "SELECT slip_id, slip_no, slip_order_date, bill_to, ship_to, reference, po_no, customer_address, sales_person, truck_id, ship_date, comments, approved_by, invoice_billed_by, invoice_no, order_status, user_id, do_no, tracking_status, assign_to, time_delivered, received_by, deliver_img FROM picking_order WHERE order_status = 'deliver' AND assign_to = ? AND tracking_status != 'Delivered'";
        $stmt = $this->prepareQuery($this->conn, $query, 'i', array((int)$id));
        return $this->fetchAssoc($stmt);
    }

    public function updateDeliveryStatus($comments, $tracking_status, $received_by, $image, $slipid, $userid, $location, $dateTime)
    {
        $query = "UPDATE picking_order SET comments = ?, tracking_status = ?, time_delivered = ?, received_by = ?, deliver_img = ?, delivery_location = ? WHERE slip_id = ? AND assign_to = ?";
        $stmt = $this->prepareQuery($this->conn, $query, 'ssssssii', array($comments, $tracking_status, $dateTime, $received_by, $image, $location, $slipid, $userid));
        return $this->execute($stmt);
    }

    public function isAllowed($userid, $slipid)
    {
        $query = "SELECT slip_id FROM picking_order WHERE 
        (slip_id = ? AND order_status = 'deliver') AND 
        (assign_to = ? AND tracking_status != 'Delivered')";
        $stmt = $this->prepareQuery($this->conn, $query, "ii", array($slipid, $userid));
        return $this->fetchCount($stmt);
    }

    public function getOrder($id)
    {
        $query = "SELECT slip_id, slip_no, slip_order_date, bill_to, ship_to, reference, po_no, customer_address, sales_person, truck_id, ship_date, comments, approved_by, invoice_billed_by, invoice_no, order_status, user_id, do_no, tracking_status, assign_to, time_delivered, received_by, deliver_img FROM picking_order WHERE order_status = 'deliver' AND slip_id = ? AND tracking_status != 'Delivered'";
        $stmt = $this->prepareQuery($this->conn, $query, 'i', array((int)$id));
        return $this->fetchAssoc($stmt);
    }

    public function login($username, $password)
    {
        $query = "SELECT user_id FROM user_account WHERE user_username = ? AND user_password = ? LIMIT 1";
        $stmt = $this->prepareQuery($this->conn, $query, 'ss', array($username, $password));
        return $this->fetchAssoc($stmt);
    }

    public function updateLocation($userid, $location, $timestamp)
    {
        $query = "INSERT INTO user_location(user_id, location, timestamp) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE user_id = ?, location = ?, timestamp = ?";
        $stmt = $this->prepareQuery($this->conn, $query, 'ississ', array($userid, $location, $timestamp, $userid, $location, $timestamp));
        return $this->execute($stmt);
    }


}