<?php
require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "controller.auth.php";

require_once "../model/model.inout.php";

$inout = new Inout();
$auth = new Auth();
$mode = Sanitizer::filter('mode', 'get');
$user_name = $auth->getSession("name");

switch($mode) {

    case "getProductUnit";
        $product_id = Sanitizer::filter('product_id', 'post');
        $unit_product = $inout->getUnitProduct($product_id);
        foreach ($unit_product as $k=>$v) {
            $response = array("unit" => $v['unit_name'], "quantity" => $v['stock_quantity']);
        }

        break;

    case "getLotnumber";
        $product_id = Sanitizer::filter('product_id', 'get');
        $lotno = $inout->getLotnumbers($product_id);
        $option = '<option selected disabled value=""> --- SELECT LOT NUMBER --- </option>';
        foreach ($lotno as $k=>$v) {
            $option .= '<option value="'.$v['stock_id'].'"> '.$v['stock_lotno'].' ('.($v['stock_expiration_date'] == "0000-00-00" ? 'N/A' : ''.$v['stock_expiration_date'].'').')</option>';
        }
        echo $option;
        exit;

    case "getExpirationDate";
        $stock_id = Sanitizer::filter('stock_id', 'post');
        $expiration_date = $inout->getTotlaQty($stock_id);
        foreach ($expiration_date as $k=>$v) {
            $response = array("exp_date" => $v['stock_expiration_date'], "log_qty" => $v['log_qty']);
        }
        break;

    // case "updateQuantity";
        
}

echo json_encode($response);