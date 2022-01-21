<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "controller.auth.php";
require_once "controller.filevalidator.php";
require_once "../model/model.inventory.php";

$inventory = new Inventory();
$auth = new Auth();
$mode = Sanitizer::filter('mode', 'get');
$user_name = $auth->getSession("name");

switch($mode) {
    
    case "getall";
        $response = $inventory->getAllProducts();
        break;
        
    case "table";
        $inventory = $inventory->getAllProducts();
        foreach($inventory as $k=>$v) {    
        
            $inventory[$k]['quantity'] = ($v['quantity']) ?: 0; 
            $inventory[$k]['uom'] = $inventory[$k]['unit_name'];
            $inventory[$k]['action'] = '
            <button 
                class="btn btn-sm btn-primary" 
                type="button" 
                onclick="viewproduct('.$v['product_id'].',\''.$v['product_code'].'\',\''.$v['product_description'].'\',\''.$v['unit_name'].'\',\''.$v['product_expiration'].'\',\''.$v['product_type'].'\')"><i class="material-icons myicon-lg">bar_chart</i>
                Stocks
            </button>';
        }
        
        $response = array("data" => $inventory);
        break;

    case "tableDetails";
        $product_id = Sanitizer::filter('product_id', 'get');
        $inventorys = $inventory->getProductdetails($product_id);
        foreach($inventorys as $k=>$v) {

            $location = $location_type = $v['location_type'];
            $inventorys[$k]['stock_serialno'] = ($v['stock_serialno']) ?: 'N/A'; 
            $inventorys[$k]['uom'] = $inventorys[$k]['unit_name'];
            
            switch($location_type){
                case "rak":
                    $rak_n = ($v['rak_name']) ?: false;
                    $location = '<small><b>RAK-'. $rak_n.$v['rak_column'].$v['rak_level'] . '</b></small>';
                    if(!$rak_n){
                        $location = "<small><b>DELETED RAK</b></small>";
                    }

                    break;
                case "truck":
                    $trk = ($v['truck_no']) ? 'TRK-'.$v['truck_no'] : 'DELETED TRUCK';
                    $location = '<small><b>'. $trk . '</b></small>';
                    break;
                default:
                    $crt = ($v['location_name']) ? 'CRT-'.$v['location_name'] : 'DELETED CART';
                    $location = '<small><b>'. $crt . '</b></small>';
            }

            $exp_date = $v['stock_expiration_date'];
            if($exp_date=="0000-00-00"){
                $inventorys[$k]['stock_expiration_date'] = 'N/A';
            }else{
                $inventorys[$k]['stock_expiration_date'] = $v['stock_expiration_date'];
            }
            
            $inventorys[$k]['location'] = $location;
            $action = '<button class="btn btn-sm btn-secondary" type="button">In Transit</button>'; 
            $stock_id = $v['stock_id'];
            $stock_status = $inventory->validateStock($stock_id);
            // $stock_status = "found";
            // $disabled = ($v['location_type'] == 'rak') ? '' : 'disabled';
           
            if($v['location_type'] != 'cart'){
                $action = '<button class="btn btn-sm btn-primary" type="button" onclick="editstock('.$v['stock_id'].','.$v['product_id'].','.$v['location_id'].',\''.$v['product_code'].'\',\''.$v['stock_lotno'].'\',\''.$v['stock_serialno'].'\','.$v['stock_qty'].',\''.$v['stock_expiration_date'].'\',\''.$v['log_reference'].'\',\''.$v['log_notes'].'\',\''.$v['log_transaction_date'].'\')"><i class="material-icons myicon-lg">edit</i> Edit</button>'; 
                $action .= ' <button type="button" class="btn btn-sm btn-success" onclick="printBarcode(\''.$v['stock_lotno'].'\')"><i class="material-icons myicon-lg">print</i> Barcode</button>';
                if($stock_status=="notfound"){
                    $action .= ' <button type="button" class="btn btn-sm btn-danger" onclick="deleteStock('.$v['stock_id'].','.$v['product_id'].')"><i class="material-icons myicon-lg">delete</i> Delete</button>';
                }
                
            }

            $inventorys[$k]['action'] = $action;
            
        }
        
        $response = array("data" => $inventorys);
        break;

    case "analytics":
        
        $product_id = Sanitizer::filter('product_id', 'get', 'int');
        $analytics = $inventory->getProductAnalytics($product_id);

        $cart = $analytics[0][0];
        $cart_p = $analytics[0][1];
        $rak = $analytics[1][0];
        $rak_p = $analytics[1][1];
        $trk = $analytics[2][0];
        $trk_p = $analytics[2][1];

        $response = array(
            "cart" => $cart,
            "cart_p" => $cart_p,
            "rak" => $rak,
            "rak_p" => $rak_p,
            "trk" => $trk,
            "trk_p" => $trk_p,
        );
        
        break;

    case "dropdown_rak";
        require_once "../model/model.rak.php";
        $rak = new Rak();
        $units = $rak->getAllRaks();
        $option = "<option value='' disabled='' selected=''>--Select Rak--</option>";
        foreach($units as $k=>$v) {
            $option.="<option value='".$v['rak_id']."'>RAK-".$v['rak_name'].$v['rak_column'].$v['rak_level']."</option>";
        }
        echo $option;
        exit;

    case "add";

        $product_id = Sanitizer::filter('product_id', 'post');
        $stock_qty = Sanitizer::filter('stock_qty', 'post');
        $rak_id = Sanitizer::filter('rak_id', 'post');
        $stock_lotno = Sanitizer::filter('stock_lotno', 'post');
        $stock_serialno = Sanitizer::filter('stock_serialno', 'post');
        $reference = Sanitizer::filter('reference','post');
        $notes = Sanitizer::filter('notes','post');
        $stock_expiration_date = Sanitizer::filter('stock_expiration_date', 'post');
        $transaction_date = Sanitizer::filter('transaction_date','post');
        $inventory->addNewStock($product_id,$stock_qty,$rak_id,$stock_lotno,$stock_serialno,$reference,$notes,$stock_expiration_date,$transaction_date,$user_name);
        $response = array("code"=>1, "message"=>"Stock Added");
        
        break;

    case "update";
        $stock_id = Sanitizer::filter('stock_id', 'post');
        $product_id = Sanitizer::filter('product_id', 'post');
        $stock_qty = Sanitizer::filter('stock_qty', 'post');
        $rak_id = Sanitizer::filter('rak_id', 'post');
        $stock_lotno = Sanitizer::filter('stock_lotno', 'post');
        $stock_serialno = Sanitizer::filter('stock_serialno', 'post');
        $reference = Sanitizer::filter('reference','post');
        $notes = Sanitizer::filter('notes','post');
        $stock_expiration_date = Sanitizer::filter('stock_expiration_date', 'post');
        $transaction_date = Sanitizer::filter('transaction_date','post');
        $inventory->updateStock($stock_id,$product_id,$stock_qty,$rak_id,$stock_lotno,$stock_serialno,$reference,$notes,$stock_expiration_date,$transaction_date,$user_name);
        $response = array("code"=>1, "message"=>"Stock Updated");
        break;
    
    case "deleteStock";
        $stock_id = Sanitizer::filter('stock_id', 'post');
        $product_id = Sanitizer::filter('product_id', 'post');
        $inventory->deleteStock($stock_id,$product_id,$user_name);
        $response = array("code"=>1, "message"=>"Stock Updated");
        break;

    case "upload";

    if (($_FILES['grpofile']['name']!="")){

        if(!FileValidator::allowedSize('grpofile', '30000') || !FileValidator::allowedType('grpofile', array('xlsx', 'csv'))) {
            echo json_encode(array('code'=>0,'message'=>'Invalid File.'));
            die(); 
        }

        $target_dir = "../order_files/";
        $file = $_FILES['grpofile']['name'];
        $path = pathinfo($file);
        $filename = $path['filename'];
        $ext = $path['extension'];
        $attachfile = $filename.".".$ext;
        $temp_name = $_FILES['grpofile']['tmp_name'];
        $path_filename_ext = $target_dir.$filename.".".$ext;

        $lto_upload = $target_dir.$attachfile;

        // Check if file already exists

        if (file_exists($path_filename_ext)) {
            
            $response = array('code'=>0,'message'=>'Upload failed. File already exists.');

        } else {
            move_uploaded_file($temp_name,$path_filename_ext);
        
            require 'vendornew/autoload.php';

            $reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();

            $spreadsheet = $reader->load($path_filename_ext);
            // $spreadsheet->setActiveSheetIndex(1);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $row = 0;
            $item = 0;
            $a = 2;

            $order = array();

            foreach($order as $v)
            {
                if(!$v) {
                    echo json_encode(array('code'=>0,'message'=>'Invalid Excel file.'));
                    unlink($path_filename_ext);
                    die(); 
                }
            }

            for($a==2;$a<=$highestRow;$a++){
                $product_code = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $a)->getValue();
                $product_description = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(2,$a)->getValue();
                $product_type = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(3,$a)->getValue();
                $uom = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(4,$a)->getValue();
                $quantity_ordered = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(5,$a)->getValue();
                $stock_lotno = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(6,$a)->getValue();
                $stock_serialno = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(7,$a)->getValue();
                $stock_expiration_date = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(8,$a)->getFormattedValue();



                if($product_code!=null AND $quantity_ordered!=null){
                    $inventory->addStocks($product_code,$product_description,$product_type,$uom,$quantity_ordered,$stock_lotno,$stock_serialno,$stock_expiration_date);
                }
                
            }
            
            unlink($path_filename_ext);
            
        }

        $response = array('code'=>1,'message'=>'GRPO was saved successfully');
    
    } else {

        $response = array('code'=>0,'message'=>'file upload failed');
    }

    break;

}

echo json_encode($response);
