<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "controller.auth.php";
require_once "../model/model.picking.php";

$auth = new Auth();
$picking = new Picking();
$mode = Sanitizer::filter('mode', 'get');
$user_name = $auth->getSession("name");


switch($mode) {
    
    case "add":
        $order_details_id = Sanitizer::filter('order_details_id', 'post');
        $stock_id = Sanitizer::filter('stock_id', 'post');
        $product_id = Sanitizer::filter('product_id', 'post');
        $stock_lotno = Sanitizer::filter('stock_lotno', 'post');
        $stock_serialno = Sanitizer::filter('stock_serialno', 'post');
        $stock_qty = Sanitizer::filter('stock_qty', 'post');
        $stock_expiration_date = Sanitizer::filter('stock_expiration_date', 'post');
        $pickingQuantity = Sanitizer::filter('pickingQuantity', 'post');
        $pickingCart = Sanitizer::filter('pickingCart', 'post');
        $slip_id = Sanitizer::filter('slip_id','post');
        $location_id = Sanitizer::filter('location_id','post');

        $picking->Save_selectedItem($order_details_id,$stock_id,$product_id,$stock_lotno,$stock_serialno,$stock_qty,$stock_expiration_date,$pickingQuantity,$pickingCart,$slip_id,$location_id);
        $response = array("code"=>1, "message"=>"Order Added");
        break;

    case "invoice":
        $slipId = Sanitizer::filter('id', 'get', 'int');
        $table = Sanitizer::filter('table', 'get', 'int');
        $picking->invoice($slipId, $table, $user_name);
        $response = array("code"=>1, "message"=>"Order sent to invoice");
        break;

    case "usersOption";
        $users = $picking->getAllUsers();
        $html = "";
        
        foreach($users as $k=>$v){
            $id = $users[$k]["user_id"];
            $name = $users[$k]["user_fullname"];
            $html .= "<option value='$id'>$name</option>";
        }

        $response = array("code"=>1,"html"=>$html);
        break;
    case "deleteOrder";
        $slip_id = Sanitizer::filter('slip_id', 'post', 'int');
        $picking->deleteOrder($slip_id);
        $response = array("code"=>1, "message"=> "Order Deleted");
        break;

    case "savenewUser";
        $user_id = Sanitizer::filter('users', 'post');
        $slip_id = Sanitizer::filter('slip_idforpicker', 'post');
        $picking->save_user($user_id,$slip_id);
        $response = array("code"=>1, "message"=>"User Successfully Changed");
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
        $undo_qty = Sanitizer::filter('undo_qty','post');
        $response = $picking->Undo_pickedorder($id,$stock_id,$stock_lotno,$stock_expiration_date,$stock_qty,$rak_return_id,$productid,$serial,$undo_qty);

        if($response == "invalid"){
            echo json_encode(array("stat"=>"invalid"));
            die();
        }

        echo json_encode(array("stat"=>"success"));
        die();
        
        break;

    case "validateStorage":
        $id = Sanitizer::filter('i', 'get');
        $type = Sanitizer::filter('type', 'get');
        $res = $picking->validateStorage($id,$type);

        $response = array("code"=>0, "message"=>"Temporary storage not found!");
        if($res){
            $response = array("code"=>1, "message"=>"Temporary Storage found.");
        }
        break;

    case "picking":

        error_reporting(E_ALL);

        $slip_id = Sanitizer::filter('i', 'get', 'int');
        $customer_name = Sanitizer::filter('m', 'get');
        $slip_no = Sanitizer::filter('n', 'get');
        $status = $picking->getOrderStatus($slip_id);
        $hasPending = 'disabled';
        if(!$status)
        { 
            $response = array("code"=>0, "message"=>"Picking Slip is currently being picked by someone else");
            echo json_encode($response);
            die();
        }

        $order_count = 0;
        foreach($status as $k=>$x)
        {   
            $order_status = $x['order_status'];
            $location_type = ($order_status == "picked") ? "cart" : "rak";
            // $hasPending = ($order_status == "pending") ? 'disabled' : '';
            if($order_status=="pending" || $order_status=="incomplete"){
                $order_count += 1;
            }
            // $lotId = ($order_status == "pending") ? $x["product_id"] : $x["stock_id"];
            $lotId = $x["product_id"];
            $stock_lotno = $x["stock_lotno"];
            $status[$k]["lots"] = $picking->getAllLots($lotId, $location_type,$order_status, $slip_id, $stock_lotno);
        }   
        
        $hasPending = '';
        
        if($order_count > 0){
            $hasPending = 'disabled';
        }

        ob_start();
        ?>
        <div class="pick-content">
            <div class="nav-fix-sace"></div>
            <nav aria-label="breadcrumb mt-md-5">
                <ol class="breadcrumb bg-transparent pl-0 mb-0">
                    <li class="breadcrumb-item active">Home</li>
                    <li class="breadcrumb-item active" aria-current="page">Picking</li>
                </ol>
            </nav>
            <h1 class="mt-0 s20px"><i class="mr-2">#</i> <?= $slip_no ?></h1>
            
            <br>
            
            <button type="button" class="btn btn-outline-muted border btn-sm mb-3"><i class="legend bg-warning mr-2"></i>To Pick</button>
            <button type="button" class="btn btn-outline-muted border btn-sm mb-3"><i class="legend bg-danger mr-2"></i>Incomplete</button>
            <button type="button" class="btn btn-outline-muted border btn-sm mb-3"><i class="legend bg-success mr-2"></i>Picked</button>
            
            <div class="row row-cols-1 mt-2" id="picks">
                <?php foreach($status as $k=>$v) { 
                    $prod_image = "product_image/".$v['product_code'].".jpg";
                    $border_color = "bg-success";
                    $sorter = 3;
                    switch(strtolower($v['order_status'])) {
                        case "incomplete":
                        $border_color = "bg-danger";
                        $sorter = 1;
                        break;
                        case "pending":
                        $sorter = 2;
                        $border_color = "bg-warning";
                        break;
                    }
                    ?>
                <div class="col mb-4 pick" data-id="<?= $sorter ?>">
                    
                    <div class="card-panel has-thumb p-4 mb-0 pick-main" data-target="v-<?= $v['product_id'] ?>">
                        <img class="thumb" src="<?php echo $prod_image ?>" onError="this.onerror=null;this.src='product_image/dummy.jpg';">
                        <p class="m-0 text-muted"><small><?= $v['product_code'] ?></small></p>
                        <p class="m-0 font-weight-bold mb-2"><?= $v['product_description'] ?></p>
                        <p class="m-0">

                            <button type="button" class="btn btn-outline-muted border btn-sm mb-3 card-pick-status to-pick-count"><i class="legend <?= $border_color ?> mr-2"></i>
                            <?php   
                            if($v['quantity_shipped']) { 
                                echo $v['quantity_shipped'].' - '; } 
                                echo $v['quantity_order'].' '.$v['unit_name'];
                            ?>
                            </button>    
                        </p>
                    </div>

                    <!-- id,stock_id,product_id,stock_lotno,stock_serialno,stock_qty,stock_expiration_date -->
                    
                    <?php 
                        if(empty($status[$k]["lots"])) {
                            ?>
                        <button class="card-panel pick-button border-top-0 mb-0 card-with-lot v-<?= $v['product_id'] ?>">
                            <i class="status bg-danger"></i>
                                <p class="m-0 text-left">
                                    <small>
                                        Out of stock
                                    </small>
                                </p>
                        </button>
                            <?php
                        }
                        foreach($status[$k]["lots"] as $lk=>$lv) {  
                        
                        $current_location = ($lv['loc'] == "rak") ? $lv['rak_name'].$lv['rak_column'].$lv['rak_level'] : $lv["location_name"];
                        $virgin = ($lv['loc'] == "rak") ? "" : "ispicked";
                        $quantity_remaining = $v['quantity_order'] - $v['quantity_shipped'];
                        
                        ?>

                        <button class="card-panel pick-button border-top-0 mb-0 card-with-lot <?= $virgin ?> v-<?= $v['product_id'] ?>" 
                            data-id="<?= $v['id'] ?>"
                            data-from_stock_id="<?= $lv['from_stock_id'] ?>"
                            data-stockid="<?= $lv['stock_id'] ?>"
                            data-productid="<?= $lv['product_id'] ?>"
                            data-product_code="<?= $v['product_code'] ?>"
                            data-lot="<?= $lv['stock_lotno'] ?>"
                            data-serial="<?= $lv['stock_serialno'] ?>"
                            data-qty="<?= $lv['stock_qty'] ?>"
                            data-order="<?= $v['quantity_order'] ?>"
                            data-remaining="<?= $quantity_remaining ?>"
                            data-expire="<?= $lv['stock_expiration_date'] ?>"
                            data-location_id="<?= $lv['location_id'] ?>">
                            
                            <i class="status bg-warning <?= $virgin ?>"></i>
                            <p class="m-0 text-left">
                                <small>
                                    <small class="d-block mb-n1"><b>EXP: <?= $lv['stock_expiration_date'] ?></b><br><b>QUANTITY: <?= $lv['stock_qty'] ?></b><br></small>
                                    <b class="text-muted">   
                                    <?= $lv['stock_lotno'].'</b> @ '.$current_location ?>
                                </small>
                            </p>
                            <?php if($virgin){ ?>
                            <div class="controls">
                                <a href="#!" tabindex="-1" style="pointer-events: none;">
                                    <i class="material-icons">refresh</i>
                                </a>
                            </div>
                            <?php }else{ ?>
                            <div class="controls">
                                <a href="#!" tabindex="-1"
                                data-id="<?= $v['id'] ?>"
                                data-from_stock_id="<?= $lv['from_stock_id'] ?>"
                                data-stockid="<?= $lv['stock_id'] ?>"
                                data-productid="<?= $lv['product_id'] ?>"
                                data-product_code="<?= $v['product_code'] ?>"
                                data-lot="<?= $lv['stock_lotno'] ?>"
                                data-serial="<?= $lv['stock_serialno'] ?>"
                                data-qty="<?= $lv['stock_qty'] ?>"
                                data-order="<?= $v['quantity_order'] ?>"
                                data-remaining="<?= $quantity_remaining ?>"
                                data-expire="<?= $lv['stock_expiration_date'] ?>"
                                data-location_id="<?= $lv['location_id'] ?>"
                                 class="manual_btn" onclick="manualInput()">
                                    <i class="material-icons">touch_app</i>
                                </a>
                            </div>

                            <?php } ?>
                        </button>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
            
            <div>
                <button class="btn btn-lg pr btn-primary" <?= $hasPending ?> data-toggle="modal" data-target="#table-modal" onclick="toTable('<?= $slip_id?>')">Submit Items</button>
            </div>

        </div>
        <?php

        $view = ob_get_clean();

        
        /* echo '<pre>';
        print_r($status);
        die();  */
       
        $response = array("code"=>1, "view"=>$view);            
        break;
}


echo json_encode($response);

