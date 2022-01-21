<?php

class Sanitizer {
    
    public static function filter($variable, $method, $type=null) {
        switch($method) {
            case 'post':
                $var = isset($_POST[$variable]) ? $_POST[$variable] : NULL;
                break;
            case 'get':
                $var = isset($_GET[$variable]) ? $_GET[$variable] : NULL;
                break;
            default:
                $var = !empty(trim($variable)) ? $variable : NULL;
                break;
        }
        
        if(!$var) { return NULL; }
        return self::sanitize($var, $type);
    }
    
    public static function sanitize($variable, $type=null) {

        $type = self::type($type);

        $strip = strip_tags($variable);
        $trim = trim($strip);
        $sanitized = filter_var($trim, $type);

        return $sanitized;
    }

    public static function type($type=null) {
        switch($type) {
            case 'int':
                $type = FILTER_SANITIZE_NUMBER_INT;
                break;
            case 'float':
                $type = FILTER_SANITIZE_NUMBER_FLOAT;
                break;
            case 'email':
                $type = FILTER_SANITIZE_EMAIL;
                break;
            default:
                $type = FILTER_SANITIZE_STRING;
                break;
        }
        return $type;
    }

    public static function sanitizeHTML($html) {
        if(!isset($_POST[$html]) || empty(trim($_POST[$html]))) { return false; }
        return $_POST[$html];
    }

    	
}