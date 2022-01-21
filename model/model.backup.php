<?php

class Backup extends DBHandler {
    
    private $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    public function getAllDatabase()
    {
        $query = "SELECT * FROM dbase";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getAllTables()
    {
        $query = "SHOW TABLES";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getCreatetable($table)
    {
        $query = "SHOW CREATE TABLE $table";
        $stmt = $this->prepareQuery($this->conn, $query);
        $row = $this->fetchRow($stmt);
        return $row[1];
    }

    public function getCountfromTable($table)
    {
        $query = "SELECT * FROM $table";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchCount($stmt);
    }

    public function getdatafromTable($table)
    {
        $query = "SELECT * FROM $table";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function getCountcolumnTable($table)
    {
        $query = "SHOW COLUMNS FROM $table";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchCount($stmt);
    }

    public function getcolfromTable($table)
    {
        $query = "SHOW COLUMNS FROM $table";
        $stmt = $this->prepareQuery($this->conn, $query);
        return $this->fetchAssoc($stmt);
    }

    public function SaveDatabase($backup_file_name,$date_today,$user_name)
    {
        $query = "INSERT INTO dbase(database_name, database_date) VALUES(?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "ss", array($backup_file_name,$date_today));
        $this->execute($stmt);

        $audit_action = 'Generate database "'.$backup_file_name.'"';
        $audit_date = date('F d, Y h:i:s');
        $query = "INSERT INTO audit_trail(audit_action, audit_date, audit_user) VALUES(?,?,?)";
        $stmt = $this->prepareQuery($this->conn, $query, "sss", array($audit_action,$audit_date,$user_name));
        return $this->execute($stmt);
    }
    
}