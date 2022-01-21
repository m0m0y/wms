<?php

class User extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllUsers()
    {
        $query = "SELECT user_id, user_fullname, user_username, user_password, user_usertype FROM user_account";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }
 
    public function getUser($id)
    {
        $query = "SELECT user_id, user_fullname, user_username, user_password, user_usertype FROM user_account WHERE user_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        return $this->fetchRow($stmt);
    }

    public function addUser($name,$username,$password,$usertype,$user_name)
    {
        $query = "INSERT INTO user_account(user_fullname, user_username, user_password, user_usertype) VALUES(?,?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "ssss", array($name,$username,$password,$usertype));
        $this->execute($stmt);

        $audit_action = 'Added new user account for "'.$name.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

    public function login($username, $password)
    {

        $query = "SELECT user_id, user_fullname, user_usertype FROM user_account WHERE user_username = ? AND user_password = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "ss", array($username, $password));
        $response = $this->fetchRow($stmt);
        return ($response) ?: false;
    }

    public function updateUser($id, $name,$username,$password,$usertype,$user_name)
    {
        $query = "SELECT user_fullname FROM user_account WHERE user_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $row = $this->fetchRow($stmt);
        $user_fullname = $row[0];

        $query = "UPDATE user_account SET user_fullname = ?,user_username = ?,user_password = ?, user_usertype = ? WHERE user_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "ssssi", array($name,$username,$password,$usertype, $id));
        $this->execute($stmt);

        $audit_action = 'Edit user account "'.$user_fullname.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

    public function deleteUser($id,$user_name)
    {
        $query = "SELECT user_fullname FROM user_account WHERE user_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $row = $this->fetchRow($stmt);
        $user_fullname = $row[0];

        $query = "DELETE FROM user_account WHERE user_id = ?";
        $stmt = $this->prepareQuery($this->conn, $query, "i", array($id));
        $this->execute($stmt);

        $audit_action = 'Delete user account of "'.$user_fullname.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }

}