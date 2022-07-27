<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "controller.auth.php";
require_once "controller.filevalidator.php";
require_once "../model/model.addorder.php";

$addorder = new AddOrder();
$auth = new Auth();
$mode = Sanitizer::filter('mode', 'get');
$user_name = $auth->getSession("name");


switch($mode) {

    case "upload";

    if (($_FILES['orderfile']['name']!="")){

        if(!FileValidator::allowedSize('orderfile', '30000') || !FileValidator::allowedType('orderfile', array('xlsx', 'csv'))) {
            echo json_encode(array('code'=>0,'message'=>'Invalid File.'));
            die(); 
        }

        $target_dir = "../order_files/";
        $file = $_FILES['orderfile']['name'];
        $path = pathinfo($file);
        $filename = $path['filename'];
        $ext = $path['extension'];
        $attachfile = $filename.".".$ext;
        $temp_name = $_FILES['orderfile']['tmp_name'];
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
            $a = 8;

            $order = array();

            $slipno = $order[] = str_replace("_x000D_", " ", $spreadsheet->getActiveSheet()->getCellByColumnAndRow(2, 1)->getFormattedValue());
            $sliporder_date = $order[] = str_replace("_x000D_", " ",$spreadsheet->getActiveSheet()->getCellByColumnAndRow(2, 2)->getFormattedValue());
            $billto = $order[] = str_replace("_x000D_", " ",$spreadsheet->getActiveSheet()->getCellByColumnAndRow(4, 1)->getFormattedValue());
            $shipto = $order[] = str_replace("_x000D_", " ",$spreadsheet->getActiveSheet()->getCellByColumnAndRow(4, 2)->getFormattedValue());
            $reference = $order[] = str_replace("_x000D_", " ",$spreadsheet->getActiveSheet()->getCellByColumnAndRow(6, 1)->getFormattedValue());
            $pono = $order[] = str_replace("_x000D_", " ",$spreadsheet->getActiveSheet()->getCellByColumnAndRow(6, 2)->getFormattedValue());
            $customer_address = $order[] = str_replace("_x000D_", " ",$spreadsheet->getActiveSheet()->getCellByColumnAndRow(8, 1)->getValue());
            $salesperson = $order[] = str_replace("_x000D_", " ",$spreadsheet->getActiveSheet()->getCellByColumnAndRow(8, 2)->getFormattedValue());
            $shipvia = $order[] = str_replace("_x000D_", " ",$spreadsheet->getActiveSheet()->getCellByColumnAndRow(10, 1)->getFormattedValue());
            $shipdate = $order[] = str_replace("_x000D_", " ",$spreadsheet->getActiveSheet()->getCellByColumnAndRow(10, 2)->getFormattedValue());

            foreach($order as $v) {
                if(!$v) {
                    echo json_encode(array('code'=>0,'message'=>'Invalid Excel file.'));
                    unlink($path_filename_ext);
                    die(); 
                }
            }

            $slip_id = $addorder->addOrder($slipno,$sliporder_date,$billto,$shipto,$reference,$pono,$customer_address,$salesperson,$shipvia,$shipdate,$user_name);

            for($a==8;$a<=$highestRow;$a++){
                $product_code = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $a)->getFormattedValue();
                $quantity_ordered = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(2,$a)->getFormattedValue();
                $location = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(3,$a)->getFormattedValue();
                $stock_lotno = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(4,$a)->getFormattedValue();
                if($product_code!=null AND $quantity_ordered!=null){
                    $addorder->addOrderDetails($slip_id,$product_code,$quantity_ordered,$location,$stock_lotno);
                }
                
            }
            
            unlink($path_filename_ext);
        }

        $response = array('code'=>1,'message'=>'Picking slip was sent successfully');
    
    } else {

        $response = array('code'=>0,'message'=>'file upload failed');
    }

    echo json_encode($response);

    break;


    case "getLotnumber";
        $product_id = Sanitizer::filter('product_id', 'get');
        $lotno = $addorder->getLotnumbers($product_id);
        $option = '<option selected disabled value=""> --- SELECT LOT NUMBER --- </option>';
        foreach ($lotno as $k=>$v) {
            $option .= '<option value="'.$v['stock_id'].'"> '.$v['stock_lotno'].' ('.$v['stock_expiration_date'].')</option>';
        }
        echo $option;
        exit;

    case "addOrderManual";
        $slipno = $_POST["slip_no"];
        $sliporder_date = $_POST["slip_order_date"];
        $billto = $_POST["bill_to"];
        $shipto = $_POST["ship_to"];
        $reference = $_POST["reference"];
        $pono = $_POST["po_no"];
        $customer_address = $_POST["address"];
        $salesperson = $_POST["sales_person"];
        $shipvia = $_POST["ship_via"];
        $shipdate = $_POST["ship_date"];

        $product_code = $_POST["product_codes"];
        $quantity_ordered = $_POST["order_qty"];
        $stock_lotno = $_POST["lotno"];
        $location = $_POST["location"];

        $slip_id = $addorder->addOrder($slipno,$sliporder_date,$billto,$shipto,$reference,$pono,$customer_address,$salesperson,$shipvia,$shipdate,$user_name);
        
        if($product_code!=null AND $quantity_ordered!=null){
            $addorder->addOrderManualDetails($slip_id,$product_code,$quantity_ordered,$location,$stock_lotno);
        }

        $response = array('code'=>1,'message'=> "Picking slip was sent successfully");

        echo json_encode($response);
        break;

    default:
        echo json_encode(array('code'=>0, 'message'=>'mode not found'));
        break;
}


