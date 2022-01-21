<?php

class Auth {

	function __construct() {
        $this->sessionStart();
    }
    
    function sessionStart() {
        // ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/session/'));
        if(!isset($_SESSION)) {
            session_start();
        }
    }

    function getSession($var) {
        return isset($_SESSION[$var]) && !empty($_SESSION[$var]) ? $_SESSION[$var] : false;
    }

    function setSession($var, $val) {
        $_SESSION[$var] = $val;
    }

    function compareSession($var, $expected) {
        if(is_array($expected)){
            return (in_array($var, $expected)) ? true : false;
        }
        $ses = isset($_SESSION[$var]) && !empty($_SESSION[$var]) ? $_SESSION[$var] : false;
        return $ses === $expected ? true : false;
    }

    function sessionDie($location=null) {
        session_unset();
        session_destroy();
        if($location) {
            header('location: ' . $location);
            exit();
        }
    }

    function forbid($var, $expected) {
        if (!$this->compareSession($var, $expected)) {
            echo "Forbidden";
            die();
        }
    }

    function redirect($var, $expected, $location=null) {
        if (!$this->compareSession($var, $expected)) {
            if($location) {
                header('location: ' . $location);
            }
            exit();
        } 
    }

}