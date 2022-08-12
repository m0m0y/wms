<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "controller.auth.php";
require_once "../model/model.invoicing.php";

$auth = new Auth();
$invoicing = new Invoicing();
$mode = Sanitizer::filter('mode', 'get');
$user_name = $auth->getSession("name");


switch($mode) {
    
    case "repick";
        $slip_id = Sanitizer::filter('slip_id', 'post');
        $comments = Sanitizer::filter('comments', 'post');

        $invoicing->repick_Order($slip_id,$comments,$user_name);
        $response = array("code"=>1, "message"=>"Repick Sent");
        break;

    case "check";
        $slip_id = Sanitizer::filter('slip_id', 'post');

        $invoicing->check_Order($slip_id,$user_name);
        $response = array("code"=>1, "message"=>"Order Packed");
        break;

    case "count";
        $response = array("count"=>$invoicing->countInvoices());
        break;
    
    case "invoice";
        $slipid = Sanitizer::filter('slipid', 'post');
        $invoiceno = Sanitizer::filter('invoiceno', 'post');
        $invoicing = $invoicing->updateInvoiceNum($slipid, $invoiceno);
        
        $response = array("code"=>1,"message"=>"Successfully");
        break;    
}


echo json_encode($response);

