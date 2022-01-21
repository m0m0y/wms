<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "../model/model.truck.php";

$truck = new Truck();
$mode = Sanitizer::filter('mode', 'get');


switch($mode) {
    
    case "getall";
        $response = $truck->getAllTrucks();
        break;
        
    case "table";
    
        $truck = $truck->getAllTrucks();

        foreach($truck as $k=>$v) {
            $truck[$k]['action'] = '<button class="btn btn-sm btn-primary" type="button" onclick="edittruck('.$v['truck_id'].',\''.$v['truck_no'].'\')">edit</button> ';
            $truck[$k]['action'] .= '<button class="btn btn-sm btn-danger" type="button" onclick="deletetruck('.$v['truck_id'].',\''.$v['truck_no'].'\')">delete</button>';
        }
        
        $response = array("data" => $truck);
        break;

    case "get";
        $id = Sanitizer::filter('id', 'get', 'int');
        $response = $truck->getTruck($id);
        break;

    case "add";
        $name = Sanitizer::filter('truck_no', 'post');
        $status = 1;
        $truck->addTruck($name,$status);
        $response = array("code"=>1, "message"=>"Truck Added");
        break;

    case "update";
        $name = Sanitizer::filter('truck_no', 'post');
        $status = 1;
        $id = Sanitizer::filter('truck_id', 'post', 'int');
        $truck->updateTruck($id, $name,$status);
        $response = array("code"=>1, "message"=>"Truck Updated");
        break;

    case "delete";
        $id = Sanitizer::filter('truck_id', 'post', 'int');
        $truck->deleteTruck($id);
        $response = array("code"=>1, "message"=>"Truck Deleted");
        break;
}


echo json_encode($response);

