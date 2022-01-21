<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "../model/model.report.php";

$stockcard = new Stockcard();
$mode = Sanitizer::filter('mode', 'get');


switch($mode) {
    
    case "option_product";
        $stockcard = $stockcard->getAllProducts();
        $html = "";
        
        foreach($stockcard as $k=>$v){
            $id = $stockcard[$k]["product_id"];
            $name = $stockcard[$k]["product_code"].' ( '.$stockcard[$k]["product_description"].' )';
            $html .= "<option value='$id'>$name</option>";
        }

        $response = array("code"=>1,"html"=>$html);
        break;

    case "option_product_select";
        $stockcard = $stockcard->getAllProducts();
        $html = "";
        
        foreach($stockcard as $k=>$v){
            $id = $stockcard[$k]["product_code"];
            $name = $stockcard[$k]["product_code"];
            $html .= "<option value='$id'>$name</option>";
        }

        $response = array("code"=>1,"html"=>$html);
        break;    

    case "option_lot";

        $product = Sanitizer::filter('product', 'post');
        $lots = $stockcard->getAllLots($product);
        $html = "";
        
        foreach($lots as $k=>$v){
            $id = $lots[$k]["stock_id"];
            $name = $lots[$k]["stock_lotno"].' ( '.$lots[$k]["location_type"].' '.$lots[$k]["location_id"].' ) ';
            $html .= "<option value='$id'>$name</option>";
        }

        $response = array("code"=>1,"html"=>$html);
        break;

    case "delete_rr":
        require_once "../model/model.receiving.php";
        
        $report_id = Sanitizer::filter('report_id', 'get', 'int');
        $report = new Receiving();
        $report = $report->deleteReport($report_id);
        $response = array("code"=>1,"message"=>"Report Deleted");
        break;

    case "select_item":
        require_once "../model/model.receiving.php";

        $item_code = Sanitizer::filter('item_code', 'post');
        $report = new Receiving();
        $reports = $report->selectItem($item_code);
        $response = $reports;
        break;
        
}


echo json_encode($response);

