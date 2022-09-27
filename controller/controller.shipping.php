<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "controller.auth.php";
require_once "../model/model.shipping.php";

$auth = new Auth();
$shipping = new Shipping();
$mode = Sanitizer::filter('mode', 'get');
$user_name = $auth->getSession("name");


switch($mode) {
    
    case "add":
        $box_number = Sanitizer::filter('box_number', 'post');
        $pickingCart = Sanitizer::filter('pickingCart', 'post');
        $bn = explode(",",$box_number);
        $bn_count = count($bn);
        $a = 0;
        for($a==0;$a<$bn_count;$a++){
            $boxnumber = $bn[$a];
            $shipping->Save_selectedItem($boxnumber,$pickingCart);
        }
        $response = array("code"=>1, "message"=>$box_number);
        break;

    case "deliver":
        $slip_id = Sanitizer::filter('slip_id','post');
        $do_number = Sanitizer::filter('do_number','post');
        $assign_to = Sanitizer::filter('assign_to','post');
        $shipping->deliverOrder($slip_id,$do_number,$assign_to,$user_name);
        $response = array("code"=>1, "message"=>"Deliver order");
        break;

    case "option";
        $shipping = $shipping->getAllDispatcher();
        $html = "";
        $html .= "<option value='0'>NONE</option>";
        foreach($shipping as $k=>$v){
            $id = $shipping[$k]["user_id"];
            $name = $shipping[$k]["user_fullname"];
            $html .= "<option value='$id'>$name</option>";
        }

        $response = array("code"=>1,"html"=>$html);
        break;

    case "undo":
        $box_number = Sanitizer::filter('box_number', 'post');
        $rak_return_id = Sanitizer::filter('rak_return_id', 'post');
        $response = $shipping->Undo_pickedorder($box_number,$rak_return_id);

        if($response == "invalid"){
            echo json_encode(array("stat"=>"invalid"));
            die();
        }

        echo json_encode(array("stat"=>"success"));
        die();
        
        break;

    case "repack":
        $slip_id = Sanitizer::filter('slip_id', 'post');
        $shipping->repack_order($slip_id);
        $response = array("code"=>1, "message"=>"Repack Order");
        break;

    case "validateStorage":
        $id = Sanitizer::filter('i', 'get');
        $type = Sanitizer::filter('type', 'get');
        $res = $shipping->validateStorage($id,$type);

        $response = array("code"=>0, "message"=>"Temporary storage not found!");
        if($res){
            $response = array("code"=>1, "message"=>"Temporary Storage found.");
        }
        break;

    case "shipping":
        $slip_id = Sanitizer::filter('i', 'get', 'int');
        $customer_name = Sanitizer::filter('m', 'get');
        $slip_no = Sanitizer::filter('n', 'get');
        $status = $shipping->getOrderStatus($slip_id);
        $hasPending = '';
        if(!$status)
        { 
            $response = array("code"=>0, "message"=>"Picking Slip is currently being picked by someone else");
            echo json_encode($response);
            die();
        }

        $order_count = 0;
        foreach($status as $k=>$x)
        {   
            $order_status = $x['box_number_Status'];
            if($order_status==""){
                $order_count += 1;
            }

        }   
        
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
                    <li class="breadcrumb-item active" aria-current="page">Shipping</li>
                </ol>
            </nav>
            <h1 class="mt-0 s20px"><i class="mr-2">#</i> <?= $slip_no ?></h1>
            
            <br>
            
            <div class="row row-cols-1 mt-2" id="picks">
                <?php $boxs = "";
                 foreach($status as $k=>$v) { 
                    if($v['box_number_Status']!="Moved"){
                        $boxs .= $v['box_number'].',';                        
                    }
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
                    $moved_box = ($v['box_number_Status']=="") ?"" : "";
                    $tabindex = ($v['box_number_Status']=="") ? "1" : "";
                    $aaa = ($v['box_number_Status']=="") ? "" : "aaa";

                    ?>
                <div class="col mb-4 pick" data-id="<?= $sorter ?>">

                    <div class="card-panel has-thumb p-4 mb-0 pick-main card_box <?= $aaa ?>" style="<?= $moved_box ?>" data-box_number="<?= $v['box_number'] ?>" tabindex="<?= $tabindex ?>">
                        <img class="thumb" src="static/box.png">
                        
                        <p class="m-0 text-muted"><small>BOX NUMBER:</small></p>
                        <p class="m-0 font-weight-bold mb-2"><?= $v['box_number'] ?></p>

                        <div class="controls">
                            <a href="#!" tabindex="-1" class="manual_btn" data-box_number="<?= $v['box_number'] ?>" onclick="manualInput(<?= $slip_id ?>)">
                                <i class="material-icons">touch_app</i>
                            </a>
                        </div>


                        <?php if($v['box_number_Status']=="Moved"){ ?>
                            <div class="controls">
                                <a href="#!" tabindex="-1" style="pointer-events: none;">
                                    <i class="material-icons">refresh</i>
                                </a>
                            </div>
                        <?php } ?>

                       <!--  <?php if($v['box_number_Status']!="Moved"){ ?>
                            <div class="controls">
                                <label class="container">
                                    <input type="checkbox" name="box_number_cbox" value="<?= $v['box_number'] ?>" id="box_number_cbox">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                        <?php } ?> -->


                    </div>

                    

                </div>
                <?php }
                $boxs = substr($boxs, 0, -1);

                 ?>

            </div>

            <div>
                <input type="hidden" value="<?= $boxs ?>" id="boxx" name="">
                <!-- <button class="btn btn-lg pr btn-info mb-1" style="<?= $hasBox ?>" onclick="movetoDeliver()">Move to Truck</button> -->
                <button class="btn btn-lg pr btn-primary" <?= $hasPending ?> onclick="toDeliver('<?= $slip_id?>')">Deliver Order</button>
            </div>

        </div>
        <?php

        $view = ob_get_clean();
       
        $response = array("code"=>1, "view"=>$view);            
        break;

    case "dropdown_truck";
        require_once "../model/model.cart.php";
        $truck = new Cart();
        $units = $truck->getTruckOnly();
        $option = "<option value='' disabled='' selected=''>--Select Truck--</option>";
        foreach($units as $k=>$v) {
            $option.="<option value='".$v['cart_id']."'>".$v['location_name']."</option>";
        }
        echo $option;
        exit;
}


echo json_encode($response);

