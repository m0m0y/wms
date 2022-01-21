<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "controller.auth.php";
require_once "../model/model.packing.php";

$auth = new Auth();
$packing = new Packing();
$mode = Sanitizer::filter('mode', 'get');
$user_name = $auth->getSession("name");


switch($mode) {
    
    case "ship";
        $slip_id = Sanitizer::filter('slip_id', 'post');
        $comments = Sanitizer::filter('comments', 'post');

        $packing->ship_Order($slip_id,$comments,$user_name);
        $response = array("code"=>1, "message"=>"Repick Sent");
        break;

    case "check";
        $slip_id = Sanitizer::filter('slip_id', 'post');
        $comments = Sanitizer::filter('comments', 'post');
        $packing->check_Order($slip_id,$comments,$user_name);
        $response = array("code"=>1, "message"=>"Check Order");
        break;

    case "boxing";
        $box_number = Sanitizer::filter('box_number', 'post');
        $picking_order_ids = Sanitizer::filter('picking_order_ids', 'post');

        $packing->box_Order($box_number,$picking_order_ids);
        $response = array("code"=>1, "message"=>"Orders Successfully Boxed");
        break; 

    case "undo";
        $id = Sanitizer::filter('id', 'post');

        $packing->undo_BoxedItem($id);
        $response = array("code"=>1, "message"=>"Item Unboxing :D");
        break;

    case "undoall";
        $slip_id = Sanitizer::filter('slip_id', 'post');

        $packing->undoall_BoxedItem($slip_id);
        $response = array("code"=>1, "message"=>"Item Unboxing :D");
        break;    

    case "option";
        $slip_id = Sanitizer::filter('slip_id', 'post');
        $units = $packing->getAllBoxno($slip_id);
        $html = "";
        
        foreach($units as $k=>$v){
            $id = $units[$k]["box_number"];
            $name = $units[$k]["box_number"];
            $html .= "<option value='$id'>$name</option>";
        }

        $response = array("code"=>1,"html"=>$html);
        break;    

}


echo json_encode($response);

