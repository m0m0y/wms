<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "../model/model.transferitem.php";

$transfer = new Transfer();
$mode = Sanitizer::filter('mode', 'get');


switch($mode) {
        
    case "table";
        $approve = "Approve";
        $disapprove = "Disapprove";
        $undo = "Undo";
        $transfer = $transfer->getAllTransfer();
        foreach($transfer as $k=>$v) {

            $transfer[$k]['location'] = ($v['b_rakname'].$v['b_rakcolumn'].$v['b_raklevel']) ?: "-";
            $transfer[$k]['moving_to'] = ($v['rak_name'].$v['rak_column'].$v['rak_level']) ?: "-";
            $transfer_status = $v['transfer_status'];
            $notgrpo = $transfer[$k]['requested_by'];
            $transfer[$k]['requested_by'] = ($transfer[$k]['user_fullname']) ?: 'From GRPO';
            $statusClass = $text = "text-primary";

            switch($transfer[$k]['transfer_status']) {
                case "Pending":
                    $statusClass = "text-warning";
                    $text = "Pending";
                    break;
                case "Disapprove":
                    $statusClass = "text-danger";
                    $text = "Disapproved";
                    break;
                case "Approve":
                    $statusClass = "text-success";
                    $text = "Approved";
                    break;
                default:
                    $text = "Completed";
                    break;
            }

            $transfer[$k]['transfer_status'] = '<span class="'.$statusClass.'">' . $text . '</span>';

            if($notgrpo) {
                if($transfer_status=="Approve"){
                    $transfer[$k]['action'] = '<button class="btn btn-sm btn-secondary" type="button" onclick="reviewTransfer('.$v['id'].',\''.$undo.'\')"><i class="material-icons myicon-lg">replay</i></button> ';
                }else if($transfer_status=="Disapprove"){
                    $transfer[$k]['action'] = '<button class="btn btn-sm btn-success" type="button" onclick="reviewTransfer('.$v['id'].',\''.$undo.'\')"><i class="material-icons myicon-lg">replay</i></button> ';
                    $transfer[$k]['action'] .= '<button class="btn btn-sm btn-danger" type="button" onclick="deleteTransfer('.$v['id'].')"><i class="material-icons myicon-lg">delete</i></button>';
                }else if($transfer_status=="Finished" || $transfer_status=="Picked"){
                    $transfer[$k]['action'] = '<button class="btn btn-sm btn-primary" type="button"><i class="material-icons myicon-lg">done_all</i></button>';
                }else{
                    $transfer[$k]['action'] = '<button class="btn btn-sm btn-success" type="button" onclick="reviewTransfer('.$v['id'].',\''.$approve.'\')"><i class="material-icons myicon-lg">done</i></button> ';
                    $transfer[$k]['action'] .= '<button class="btn btn-sm btn-danger" type="button" onclick="reviewTransfer('.$v['id'].',\''.$disapprove.'\')"><i class="material-icons myicon-lg">clear</i></button>';
                }
            } else {
                $transfer[$k]['action'] = '<button class="btn btn-sm btn-info requestee" data-max="'.$v['stock_qty_moving'].'" data-to="'.$v['product_code'].'" data-id="'.$v['id'].'" type="button">Breakdown</button> ';
            }

        }

        $response = array("data" => $transfer);
        break;

    case "review";
        $id = Sanitizer::filter('id', 'post', 'int');
        $statuss = Sanitizer::filter('statuss', 'post');
        $transfer->reviewTransfer($id, $statuss);
        $response = array("code"=>1, "message"=>$statuss);
        break;

    case "delete";
        $id = Sanitizer::filter('id', 'post', 'int');
        $transfer->delete_Transfer($id);
        $response = array("code"=>1, "message"=>$statuss);
        break;

    case "getstockid":
        $lot = Sanitizer::filter('lot', 'get');
        $view = $transfer->getStockIds($lot);
        
        $option = "<option>Invalid Stock Number</option>";

        $code = 0;

        if(!empty($view)){
            $code = 1;
            ob_start();
            $option = "<option value='0' selected>select location</option>";
            foreach($view as $key => $value) {
                $option .= "<option value='". $value["stock_id"] ."' data-qty='". $value["stock_qty"] ."'>".$value["rak_name"].$value["rak_column"].$value["rak_level"] ."</option>";
            }
        }

        $response = array("code"=>$code, "message"=>$view, "view"=>$option);
        break;

    case "getraks":
        
        require_once "../model/model.rak.php";
        $rak = new Rak();
        $units = $rak->getAllRaks();
    
        $option = "<option value='' disabled='' selected=''>--Select Rak--</option>";
        foreach($units as $k=>$v) {
            $option.="<option value='".$v['rak_id']."'>RAK-".$v['rak_name'].$v['rak_column'].$v['rak_level']."</option>";
        }
        $response = array("code"=>0, "message"=>"loaded dropdown", "view"=>$option);
        break;

    case "request":
        require_once "controller.auth.php";
        
        $auth = new Auth();
        
        /* Prepare Data */
        
        $requester_id = $auth->getSession('logid');
        $request_id = Sanitizer::filter('request_id', 'post', 'int');
        $to_move = Sanitizer::filter('to_move', 'post');
        $to_rak = Sanitizer::filter('to_rak', 'post');
        $current_qty = $transfer->getQty($request_id);

        /* Validate current request */

        if(!$current_qty) { 
            $response = array("code"=>0, "message"=>"Base request not found.");
            echo json_encode($response);    
            die(); }

        if($to_move > $current_qty[1]) {
            $response = array("code"=>0, "message"=>"New 'to move' quantity can't be greater than the original quantity.");
            echo json_encode($response);
            die(); }

        /* Update current request */

        $new_to_move = (int)$current_qty[1] - $to_move;
        if($to_move == $current_qty[1]) {
            $transfer->delete_Transfer($request_id);
        } else {
            $transfer->updateQty($request_id, $new_to_move);
        }

        /* Insert broken down request */

        $transfer->addTransfer($current_qty[0], $to_rak, $to_move, $requester_id);

        $response = array("code"=>1, "message"=>$statuss);


        break;

}


echo json_encode($response);

