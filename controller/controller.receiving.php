<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "controller.filevalidator.php";
require_once "controller.auth.php";
require_once "../model/model.receiving.php";

$auth = new Auth();
$receiving = new Receiving();
$mode = Sanitizer::filter('mode', 'get');


switch($mode) {

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
            $a = 8;

            $receiving_report = array();

            $po_no = $receiving_report[] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(2, 1)->getFormattedValue();
            $company_name = $receiving_report[] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(6, 1)->getFormattedValue();
            $origin = $receiving_report[] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(4, 1)->getFormattedValue();
            $type = "";
            $kind = "";
            $no_package = 0;
            $control_no = 0;
            $remarks = "";
            $disposition = "";
            $reference = $receiving_report[] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(8, 1)->getFormattedValue();
            $delivery = $receiving_report[] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(5, 8)->getFormattedValue();
            $date_added = $receiving_report[] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(2, 2)->getFormattedValue();
            $report_status = 0;
            $total_weight = "";            


            $report_id = $receiving->addReport($company_name, $origin, $type, $kind, $control_no, $remarks, $disposition, $reference, $delivery, $total_weight);

            for($a==8;$a<=$highestRow;$a++){
                $item_code = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $a)->getFormattedValue();
                $item_lot = $item_code.$po_no;
                $item_description = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(2,$a)->getFormattedValue();
                $expected_date = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(5,$a)->getFormattedValue();
                $item_expiry_month = date('M', strtotime($expected_date));
                $item_expiry_year = date('Y', strtotime($expected_date));
                $item_unit = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(4,$a)->getFormattedValue();
                if($item_code!=null AND $item_description!=null){
                    $receiving->addItem($report_id, $item_code, $item_lot, $item_description, $item_expiry_month, $item_expiry_year, $item_unit);
                }
                
            }
            
            unlink($path_filename_ext);
            
        }

        $response = array('code'=>1,'message'=>'PO was saved successfully');
    
    } else {

        $response = array('code'=>0,'message'=>'file upload failed');
    }

    break;

    case "add";

        $fields = array('company', 'origin','type', 'kind', 'control_no','remarks', 'disposition', 'ref_no', 'date_delivery', 'total_weight');
        foreach($fields as $field) {
            ${$field} = Sanitizer::filter($field, 'post');
        }

        $report_id = $receiving->addReport($company, $origin, $type, $kind, $control_no, $remarks, $disposition, $ref_no, $date_delivery, $total_weight);
        $item_code = (isset($_POST['item_code'])) ? $_POST['item_code'] : array();
        $item_lot = (isset($_POST['item_lot'])) ? $_POST['item_lot'] : array();
        $item_expiry_month = (isset($_POST['item_expiry_month'])) ? $_POST['item_expiry_month'] : array();
        $item_expiry_year = (isset($_POST['item_expiry_year'])) ? $_POST['item_expiry_year'] : array();
        $item_description = (isset($_POST['item_description'])) ? $_POST['item_description'] : array();
        $item_unit = (isset($_POST['item_unit'])) ? $_POST['item_unit'] : array();

        for($i = 0;$i < count($item_code);$i ++){
            $item_code_ = (array_key_exists($i, $item_code)) ? $item_code[$i] : null;
            $item_lot_ = (array_key_exists($i, $item_lot)) ? $item_lot[$i] : null;
            $item_expiry_month_ = (array_key_exists($i, $item_expiry_month)) ? $item_expiry_month[$i] : null;
            $item_expiry_year_ = (array_key_exists($i, $item_expiry_year)) ? $item_expiry_year[$i] : null;
            $item_description_ = (array_key_exists($i, $item_description)) ? $item_description[$i] : null;
            $item_unit_ = (array_key_exists($i, $item_unit)) ? $item_unit[$i] : null;
            $receiving->addItem($report_id, $item_code_, $item_lot_, $item_description_, $item_expiry_month_, $item_expiry_year_, $item_unit_);        
        }

        $response = array("code" => 55, "message" => "Receiving Report Added", "path" => "receiving-report.php?validate=$report_id");
        break;

    case "table";
        $report_status = Sanitizer::filter('report_status', 'get');
        $receiving = $receiving->getAllReports($report_status);
        foreach($receiving as $k=>$v) {

            $receiving[$k]['notes'] = '<span class="btn btn-sm bg-muted px-4" style="white-space: nowrap">'.$v['type'].'</span> ';
            $receiving[$k]['notes'] .= '<span class="btn btn-sm bg-muted px-4" style="white-space: nowrap">'.$v['kind'].'</span> ';
            $receiving[$k]['notes'] .= '<span class="btn btn-sm bg-muted px-4" style="white-space: nowrap">'.$v['expected_weight'].'</span> ';
            $s_class = "";
            $s_label = "";
            if($v['report_status'] != 0){
                $s_class = "btn-primary";
                $s_label = "Picked";
            }else{
                $s_class = "btn-warning";
                $s_label = "Pending";
            }

            $receiving[$k]['notes'] .= '<span class="btn btn-sm '.$s_class.' px-4" style="white-space: nowrap">'.$s_label.'</span>';

            $receiving[$k]['action'] = '<a href="?validate='.$v['report_id'].'" class="btn btn-sm btn-primary px-4">View</a> ';
            if($v['statuss']=="Finished"){
                $receiving[$k]['action'] .= '<button class="btn btn-sm btn-danger px-4" onclick="incomplete('.$v['report_id'].')">Incomplete</button>';
            }else{
                $receiving[$k]['action'] .= '<button class="btn btn-sm btn-secondary px-4" onclick="finish('.$v['report_id'].')">Finish</button>';
            }
            
        }
        $response = array("data" => $receiving);
        break;

    case "add-qty":
        $report_id = Sanitizer::filter('id', 'get', 'int');
        $qty = Sanitizer::filter('qty', 'get', 'int');
        $receiving->updateQty($report_id, $qty);
        $response = array("code" => 1, "message" => "Quantity Updated");
        break;

        
    case "finish":
        $user_fullname = $auth->getSession("name");
        $report_id = Sanitizer::filter('id', 'get', 'int');
        $receiving->finishReceiving($report_id,$user_fullname);
        $response = array("code" => 1, "message" => "Transaction Updated");
        break;

    case "updateStatus_rr":
        $report_id = Sanitizer::filter('report_id', 'post', 'int');
        $statuss = Sanitizer::filter('statuss', 'post');
        $rep_id = $receiving->UpdatsStatusRR($report_id,$statuss);
        $response = array("code" => 1, "message" => "Transaction Finished");
        break;

    case "re":
        $report_id = Sanitizer::filter('id', 'get', 'int');
        $receiving->reReceiving($report_id);
        $response = array("code" => 1, "message" => "Transaction Updated");
        break;

    case "fetch":
        $report_id = Sanitizer::filter('id', 'get', 'id');
        $report = $receiving->getReport($report_id);

        foreach($report as $key => $v) { ?>
        <div class="pick-content">
            <div class="nav-fix-sace"></div>
            <nav aria-label="breadcrumb mt-md-5">
                <ol class="breadcrumb bg-transparent pl-0 mb-0">
                    <li class="breadcrumb-item active">Home</li>
                    <li class="breadcrumb-item active" aria-current="page">Receiving</li>
                </ol>
            </nav>
            <h1 class="mt-0 s20px"><i class="mr-2">#</i> <?= $v['control_no'] ?></h1>
            <br>
            <div class="row row-cols-1 mt-2" id="picks">
                <?php
                    foreach($v['items'] as $k => $i){
                        /* [item_id] => 57
                        [report_id] => 30
                        [item_code] => Velit soluta ut culp
                        [item_lot] => Nisi incididunt elig
                        [item_description] => Iusto voluptatem mag
                        [item_expiry_month] => jan
                        [item_expiry_year] => 2015
                        [item_unit] => Et esse similique ne
                        [item_received] => 0 */
                ?>
                <div class="col mb-4 pick">
                    <div class="card-panel has-thumb p-4 mb-0 pick-main receive-item"
                        data-lot="<?= $i['item_lot'] ?>"
                        data-code="<?= $i['item_code'] ?>"
                        data-update="<?= $i['item_code'].$i['item_lot']?>"
                        tabindex="1"> 
                        <span class="thumb" id="<?= strtolower($i['item_code'].$i['item_lot'])?>" data-id="<?= $i["item_id"] ?>"><?= $i["item_received"] ?></span>
                        <p class="m-0 text-muted">
                            <span class="d-block mt-2">
                                <small><?= $i['item_unit'] ?></small>
                            </span>
                            <small><?= $i['item_description']; ?></small>
                        </p>
                        <p class="m-0 font-weight-bold mb-2"><?= $i['item_lot'] ?></p>
                        
                        <div class="controls" style="background-color: #fff;">
                            <a href="#!" tabindex="1" class="control-anchor" data-update="<?= $i['item_code'].$i['item_lot']?>" data-id="<?= $i["item_id"] ?>">
                                <i class="material-icons">edit</i>
                            </a>
                        </div>

                    </div>
                </div>
                <?php } ?>
            </div>
            <div>
                <button class="btn btn-lg pr btn-primary" onclick="finishReceiving(<?= $report_id ?>)">Finish Receiving</button>
            </div>
        </div>
        
        <?php
        }
        exit;
}


echo json_encode($response);

