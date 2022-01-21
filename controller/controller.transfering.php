<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "../model/model.transfering.php";

$transfering = new Transfering();
$mode = Sanitizer::filter('mode', 'get');


switch($mode) {

    case "getmovable":
        $lot = Sanitizer::filter('i', 'get');

        if(!$lot) { echo json_encode(array("code"=>0, "message"=>"Please scan item's barcode to move")); exit; }
        $data = $transfering->getMovable($lot);
        if(empty($data)) { echo json_encode(array("code"=>0, "message"=>"No stock available for moving")); exit; }
        
        $option = "";
        foreach($data as $k=>$v){
            $option .= "<option value='".$data[$k]['stock_id']."' data-stock='".$data[$k]['stock_qty']."' >".$data[$k]['rak_name'].$data[$k]['rak_column'].$data[$k]['rak_level']."</option>";
        }

        $response = array("code"=>1, "message"=>$option);

        break;

    case "getlocations":
        $option = "";
        $stockid = Sanitizer::filter('stockid', 'post');
        $data = $transfering->getLocations($stockid);
        foreach($data as $k=>$v){ $option .= "<option value='".$data[$k]['rak_id']."'>".$data[$k]['rak_name'].$data[$k]['rak_column'].$data[$k]['rak_level']."</option>"; }
        $response = array("code"=>1, "message"=>$option);
        break;
    
    case "add":
        $order_details_id = Sanitizer::filter('order_details_id', 'post');
        $stock_id = Sanitizer::filter('stock_id', 'post');
        $product_id = Sanitizer::filter('product_id', 'post');
        $stock_lotno = Sanitizer::filter('stock_lotno', 'post');
        $stock_serialno = Sanitizer::filter('stock_serialno', 'post');
        $stock_qty = Sanitizer::filter('stock_qty', 'post');
        $stock_expiration_date = Sanitizer::filter('stock_expiration_date', 'post');
        $pickingQuantity = Sanitizer::filter('pickingQuantity', 'post');
        $pickingRak = Sanitizer::filter('pickingRak', 'post');
        $transfer_id = Sanitizer::filter('transfer_id','post');

        $transfering->Save_selectedItem($order_details_id,$stock_id,$product_id,$stock_lotno,$stock_serialno,$stock_qty,$stock_expiration_date,$pickingQuantity,$pickingRak,$transfer_id);
        $response = array("code"=>1, "message"=>"Order Added");
        break;

    case "validateRak";
        $id = Sanitizer::filter('location', 'post');
        $type = "rak";

        $res = $transfering->validateStorage($id,$type);

        $response = array("code"=>0, "message"=>"Temporary storage not found!");
        if($res) {
            $response = array("code"=>1, "message"=>"Temporary Storage found.");
        }
        break;

    case "addRequest";
        
        require_once "controller.auth.php";
        $auth = new Auth();
        $logid = $auth->getSession('logid');
        $stock_id = Sanitizer::filter('stock_id', 'post');
        $rak_id = Sanitizer::filter('rak_id', 'post');
        $quantity_stock = Sanitizer::filter('quantity_stock', 'post');
        if(!$stock_id || !$rak_id || !$quantity_stock){
            $response = array("code"=>0,"message"=>"Unable to submit a transfer request,<br>Please fill in all necessary field.");
            echo json_encode($response);
            die();
        }
        $transfering->addTransfer($stock_id,$rak_id,$quantity_stock,$logid);
        $response = array("code"=>1, "message"=>"Transfer request sent.");
        break;

    case "endtransaction":
        $transfer_id = Sanitizer::filter('transfer_id', 'post');
        $transfering->end_Transaction($transfer_id);
        $response = array("code"=>1,"message"=>"Transaction Finished");
        break;

    case "undo":
        $stock_id = Sanitizer::filter('stock_id', 'post');
        $id = Sanitizer::filter('id', 'post');
        $stock_lotno = Sanitizer::filter('stock_lotno', 'post');
        $stock_expiration_date = Sanitizer::filter('stock_expiration_date', 'post');
        $stock_qty = Sanitizer::filter('stock_qty', 'post');
        $rak_return_id = Sanitizer::filter('rak_return_id', 'post');
        $productid = Sanitizer::filter('productid','post');
        $serial = Sanitizer::filter('serial','post');
        $new_stock_id = Sanitizer::filter('from_stock_id', 'post');
        $transfer_id = Sanitizer::filter('transfer_id','post');
        $response = $transfering->Undo_pickedorder($id,$stock_id,$stock_lotno,$stock_expiration_date,$stock_qty,$rak_return_id,$productid,$serial,$new_stock_id,$transfer_id);
        
        if($response == "invalid") { 
            echo json_encode(array("stat"=>"invalid")); die();
        } else {
            echo json_encode(array("stat"=>"success")); die();
        }
        break;

    case "validateStorage":
        $id = Sanitizer::filter('i', 'get');
        $type = Sanitizer::filter('type', 'get');
        $res = $transfering->validateStorage($id,$type);

        $response = array("code"=>0, "message"=>"Temporary storage not found!");
        if($res) {
            $response = array("code"=>1, "message"=>"Temporary Storage found.");
        }
        break;
        case "transfering":
            $transfer_id = Sanitizer::filter('i', 'get', 'int');
            $rak_id = Sanitizer::filter('m', 'get');
            $stock_id = Sanitizer::filter('n', 'get');
            $status = $transfering->getOrderStatus($transfer_id);
            $hasPending = 'disabled';
            if(!$status) { 
                $response = array("code"=>0, "message"=>"Picking Slip is currently being picked by someone else");
                echo json_encode($response);
                die();
            }
    
            $order_count = 0;
            foreach($status as $k=>$x) {   
                $transfer_status = $x['transfer_status'];
                $new_stock_id = $x['new_stock_id'];
                $location_type = "rak";
                // $hasPending = ($order_status == "pending") ? 'disabled' : '';
                if($transfer_status=="Approve"){
                    $order_count += 1;
                }
                // $lotId = ($order_status == "pending") ? $x["product_id"] : $x["stock_id"];
                $stock_id = $x['stock_id'];
                $rak_id = $x['rak_id'];
                $stk_id = ($transfer_status=="Picked") ? $new_stock_id : $stock_id;
                $status[$k]["lots"] = $transfering->getAllLots($stk_id, $location_type,$transfer_status,$rak_id);
            }   
            
            if ($order_count > 0) {
                $hasPending = 'disabled';
            } else {
                $hasPending = '';
            }
    
                
            ob_start();
            
            ?>
            <div class="pick-content p-4">
                
                <div class="nav-fix-sace"></div>
                <nav aria-label="breadcrumb mt-md-5">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">Transfer</li>
                    </ol>
                </nav>
                <button type="button" class="btn btn-outline-warning btn-sm mb-3"><i class="legend bg-warning mr-2"></i>To Pick</button>
                <button type="button" class="btn btn-outline-success btn-sm mb-3"><i class="legend bg-success mr-2"></i>Picked</button>
                
                <div class="row row-cols-1 mt-2">
                    <?php foreach($status as $k=>$v) { 
                        $prod_image = "product_image/".$v['product_code'].".jpg";
                        if($v['b_rakname']=="" || $v['b_rakname']==null){
                            $cur_location = "Unallocated";
                        }else{
                            $cur_location  = $v['b_rakname'].$v['b_rakcolumn'].$v['b_raklevel'];
                        }
                        if($v['rak_name']=="" || $v['rak_name']==null){
                            $moving_to = "Anywhere";
                        }else{
                            $moving_to = $v['rak_name'].$v['rak_column'].$v['rak_level'];
                        }
                        
                        ?>
                    <div class="col mb-4">
                        
                        <div class="card-panel has-thumb p-4 mb-0">
                        
                            <img class="thumb" src="<?php echo $prod_image ?>" onError="this.onerror=null;this.src='product_image/dummy.jpg';">
                            <p class="m-0 text-muted"><small><?= $v['product_code'] ?></small></p>
                            <p class="m-0 font-weight-bold"><?= $v['product_description'] ?></p>
                            <p class="m-0 text-muted"><small>Current Location: <?= $cur_location ?></small></p>
                            <p class="m-0 text-muted"><small>Moving To: <?= $moving_to ?></small></p>
                            <p class="m-0">
                                <?php 
                                $zero = 0;
                                $transfering_stat = ($v['transfer_status']=="Approve") ? "ON GOING" : "PICKED";
                                $quantity_moved = ($v['transfer_status']=="Approve") ? $zero : $v['stock_qty_moving'];
                                    echo $quantity_moved.' / '.$v['stock_qty_moving'].' <b>'.$transfering_stat.'</b>';
                                      
                                ?>    
                            </p>
                        </div>
    
                        <!-- id,stock_id,product_id,stock_lotno,stock_serialno,stock_qty,stock_expiration_date -->
                        
                        <?php foreach($status[$k]["lots"] as $lk=>$lv) {  
                            
                            $current_location = ($lv['loc'] == "rak") ? $lv['rak_name'].$lv['rak_column'].$lv['rak_level'] : $lv["location_name"];
                            $virgin = ($v['transfer_status'] == "Approve") ? "" : "ispicked";
                            // $virgin = ""; 
                            $quantity_remaining = ($v['transfer_status']=="Approve") ? $v['stock_qty_moving'] : 0;
                            $rak_id = $v['rak_id'];
                            $new_stock_id = $v['new_stock_id'];
    
                            ?>
    
                            <button class="card-panel pick-button active border-top-0 mb-0 card-with-lot <?= $virgin ?>" 
                                data-id="<?= $v['id'] ?>"
                                data-from_stock_id="<?= $new_stock_id ?>"
                                data-stockid="<?= $lv['stock_id'] ?>"
                                data-productid="<?= $lv['product_id'] ?>"
                                data-lot="<?= $lv['stock_lotno'] ?>"
                                data-serial="<?= $lv['stock_serialno'] ?>"
                                data-qty="<?= $lv['stock_qty'] ?>"
                                data-product_code="<?= $v['product_code'] ?>"
                                data-remaining="<?= $quantity_remaining ?>"
                                data-rak_id="<?= $rak_id ?>"
                                data-stock_id="<?= $stock_id ?>"
                                data-transfer_id="<?= $transfer_id ?>"
                                data-expire="<?= $lv['stock_expiration_date'] ?>">
                                
                                <i class="status bg-warning <?= $virgin ?>"></i>
                                <p class="m-0 text-left">
                                    <small>
                                        <small class="d-block mb-n1"><b>EXP: <?= $lv['stock_expiration_date'] ?></b><br><b>QUANTITY: <?= $lv['stock_qty'] ?></b><br></small>
                                        <b class="text-muted">   
                                        <?= $lv['stock_lotno'].'</b> @ '.$cur_location ?>
                                    </small>
                                </p>
                                <?php if($virgin){ ?>
                                <div class="controls">
                                    <a href="#!" tabindex="-1" style="pointer-events: none;">
                                        <i class="material-icons">refresh</i>
                                    </a>
                                </div>
                                <?php } ?>
                            </button>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
    
                <div>
                    <!-- data-toggle="modal" data-target="#table-modal" -->
                    <button class="btn btn-lg pr btn-primary" <?= $hasPending ?>  onclick="toTable('<?= $transfer_id?>')">End Transaction</button>
                </div>
    
            </div>
            <?php
    
            $view = ob_get_clean();
           
            $response = array("code"=>1, "view"=>$view);            
            break;
}


echo json_encode($response);

