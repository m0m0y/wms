<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "controller.auth.php";
require_once "../model/model.cart.php";

$cart = new Cart();
$auth = new Auth();
$mode = Sanitizer::filter('mode', 'get');
$user_name = $auth->getSession("name");


switch($mode) {
    
    case "getall";
        $response = $cart->getAllCarts();
        break;
        
    case "table";
    
        $cart = $cart->getAllCarts();

        foreach($cart as $k=>$v) {
            $cartno = $v['cart_id']."**".$v['location_name'];
            $cart[$k]['action'] = '<button class="btn btn-sm btn-primary" type="button" onclick="editcart('.$v['cart_id'].',\''.$v['location_name'].'\',\''.$v['location_type'].'\')"><i class="material-icons myicon-lg">edit</i></button> ';
            $cart[$k]['action'] .= '<button class="btn btn-sm btn-danger" type="button" onclick="deletecart('.$v['cart_id'].',\''.$v['location_name'].'\')"><i class="material-icons myicon-lg">delete</i></button> ';
            $cart[$k]['action'] .= '<a class="btn btn-sm btn-success" target="_blank" href="tcpdf/examples/barcode.php?stock_lotno='.$cartno.'"><i class="material-icons myicon-lg">print</i></a>';
        
        }
        
        $response = array("data" => $cart);
        break;

    case "get";
        $id = Sanitizer::filter('id', 'get', 'int');
        $response = $cart->getCart($id);
        break;

    case "add";

        $location_name = Sanitizer::filter('location_name', 'post');
        $location_type = Sanitizer::filter('location_type', 'post');
        $status = 1;
        $cart->addCart($location_name,$location_type,$status,$user_name);
        $response = array("code"=>1, "message"=>"Location Added");
        break;

    case "update";
        $location_name = Sanitizer::filter('location_name', 'post');
        $location_type = Sanitizer::filter('location_type', 'post');
        $status = 1;
        $id = Sanitizer::filter('cart_id', 'post', 'int');
        $cart->updateCart($id, $location_name,$location_type,$status,$user_name);
        $response = array("code"=>1, "message"=>"Cart Updated");
        break;

    case "delete";
        $id = Sanitizer::filter('cart_id', 'post', 'int');
        $cart->deleteCart($id,$user_name);
        $response = array("code"=>1, "message"=>"Cart Deleted");
        break;
}


echo json_encode($response);

