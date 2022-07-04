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
            $response = array("exp_date" => $v['stock_expiration_date'], "log_qty" => $v['log_qty'], "transac_date" => $v['log_transaction_date']);
        }
        break;

    case "searchCode";
        $product_code = Sanitizer::filter('product_code', 'get');
        $search_code = $inout->getSearchProductCodes($product_code);
        foreach ($search_code as $k=>$v) {
            $response = array("product_code" => $v['product_code'], "product_description" => $v['product_description']);
        }
        break;

    case "getProductCode";
        $product_code = Sanitizer::filter('product_code', 'get');
        $searchProducts = $inout->getProductCodeWhere($product_code);
        foreach ($searchProducts as $k=>$v) {
            $option = '<option id="test" value="'.$v['product_id'].'">'.$v['product_code'].' ('.$v['product_description'].')</option>';
        }
        echo $option;
        exit;

    case "getAllProductCode";
        $allProducts = $inout->getAllProductCodes();
        $option = '<option selected disabled value=""> --- SELECT LOT NUMBER --- </option>';
        foreach ($allProducts as $k=>$v) {
            $option .= '<option value="'.$v['product_id'].'">'.$v['product_code'].' ('.$v['product_description'].')</option>';          
        }
        echo $option;
        exit;

    case "searchProductUnit";
        $product_code = Sanitizer::filter('product_code', 'post');   $product_code = Sanitizer::filter('product_code', 'post');
        $unit_product = $inout->searchUnitProduct($product_code);
        foreach ($unit_product as $k=>$v) {
            $response = array("unit" => $v['unit_name'], "quantity" => $v['stock_quantity'], "id" => $v['product_id']);
        }
        break;

    case "searchLotNum";
        $product_code = Sanitizer::filter('product_code', 'post');
        $lotno = $inout->searchLotNumbers($product_code);
        
        $response = array($lotno);
        break;
}

echo json_encode($response);