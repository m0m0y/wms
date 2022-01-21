<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "controller.auth.php";
require_once "../model/model.checking.php";

$auth = new Auth();
$checking = new Checking();
$mode = Sanitizer::filter('mode', 'get');
$user_name = $auth->getSession("name");


switch($mode) {
    
    case "pack";
        $slip_id = Sanitizer::filter('slip_id', 'post');

        $checking->pack_Order($slip_id,$user_name);
        $response = array("code"=>1, "message"=>"Order Packed");
        break;

    case "invoice";
        $slip_id = Sanitizer::filter('slip_id', 'post');

        $checking->invoice_Order($slip_id,$user_name);
        $response = array("code"=>1, "message"=>"Order Invoiced");
        break;

    case "approve";
        $id = Sanitizer::filter('id', 'post');

        $checking->approve_orderitem($id);
        $response = array("code"=>1, "message"=>"Approved Item");
        break;
    case "undo_approved";
        $id = Sanitizer::filter('id', 'post');

        $checking->undo_orderitem($id);
        $response = array("code"=>1, "message"=>"Undo Item");
        break;

    case "search":

        $search = Sanitizer::filter('s', 'get');
        $code = 2;
        $results = $checking->search($search);
        $result = $class = "";

        if(!$search) { $results = array(); }

        if(!empty($results)) {
            $code = 1;
            ob_start(); 
            foreach($results as $key=>$value) {
                
                switch($results[$key]["order_status"]){
                    case "prepare":
                    case "repick":
                        $class = "badge-warning text-white";
                        break;
                    case "ship":
                    case "invoice":
                        $class = "badge-info";
                        break;
                    case "finished":
                    case "pack":
                        $class = "badge-success";
                        break;
                    default:
                        $class = "badge-primary";
                }  
                ?>

                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="index.php?s=<?= $results[$key]["slip_no"] ?>"><?= $results[$key]["slip_no"] ?>
                    <span class="badge <?= $class ?> badge-pill ml-2 font-weight-normal"><?= $results[$key]["order_status"] ?></span>
                    </a>
                </li>
                <?php
            }
            $result = ob_get_clean();
        }

        $response = array("code"=>$code, "view"=>$result);
        break;
    
}


echo json_encode($response);

