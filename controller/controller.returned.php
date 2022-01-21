<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "controller.auth.php";
require_once "../model/model.returned.php";

$auth = new Auth();
$returned = new Returned();
$mode = Sanitizer::filter('mode', 'get');
$user_name = $auth->getSession("name");


switch($mode) {


    case "table";
        $return = $returned->getAllReturn();
        foreach($return as $k=>$v) {
            $return[$k]['product'] = $v['product_description'];
            $return[$k]['lotno'] = $v['stock_lotno'];
            $return[$k]['exp'] = $v['stock_expiration_date'];
            $return[$k]['quantity'] = $v['stock_qty'];
            $return[$k]['action'] = '<button class="btn btn-sm btn-danger" type="button" onclick="undoReturn('.$v['stock_id'].','.$v['stock_qty'].','.$v['from_stock_id'].')"><i class="material-icons myicon-lg">undo</i> UNDO</button> ';
        }
        $response = array("data" => $return);
        break;
    
    case "finished";
        $slip_id = Sanitizer::filter('slip_id', 'post');

        $returned->finished_Transaction($slip_id,$user_name);
        $response = array("code"=>1, "message"=>"Order Packed");
        break;

    case "reship";
        $slip_id = Sanitizer::filter('slip_id', 'post');

        $returned->reship_Order($slip_id);
        $response = array("code"=>1, "message"=>"Order Invoiced");
        break;

    case "quarantine";
        $returnStockid = Sanitizer::filter('returnStockid', 'post');
        $returnQty = Sanitizer::filter('returnQty', 'post');
        $quarantineArea = Sanitizer::filter('quarantineArea', 'post');

        $returned->qurantine_Item($returnStockid,$returnQty,$quarantineArea);
        $response = array("code"=>1, "message"=>"Quarantine Item");
        break;

    case "quarantineOrder";
        $slip_id = Sanitizer::filter('slip_id', 'post');
        $quarantineArea = Sanitizer::filter('quarantineArea', 'post');

        $returned->qurantine_Order($slip_id,$quarantineArea);
        $response = array("code"=>1, "message"=>"Quarantine Order");
        break;

    case "undo";
        $stock_id = Sanitizer::filter('stock_id', 'post');
        $stock_qty = Sanitizer::filter('stock_qty', 'post');
        $from_stock_id = Sanitizer::filter('from_stock_id', 'post');

        $returned->undo_Item($stock_id,$stock_qty,$from_stock_id);
        $response = array("code"=>1, "message"=>"Undo Item");
        break;

    case "getQuarantine";
        $id = $returned->getQuarantine();
        $response = array("code"=>$id, "message"=>"Success");
        break;
    
}


echo json_encode($response);

