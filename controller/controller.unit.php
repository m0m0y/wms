<?php
require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "controller.auth.php";
require_once "../model/model.unit.php";

$auth = new Auth();
$unit = new Unit();
$mode = Sanitizer::filter('mode', 'get');
$user_name = $auth->getSession("name");


switch($mode) {
    
    case "getall";
        $response = $unit->getAllUnits();
        break;

        
    case "option";
        $units = $unit->getAllUnits();
        $html = "";
        
        foreach($units as $k=>$v){
            $id = $units[$k]["unit_id"];
            $name = $units[$k]["unit_name"];
            $html .= "<option value='$id'>$name</option>";
        }

        $response = array("code"=>1,"html"=>$html);
        break;
        
    case "table";
        $unit = $unit->getAllUnits();
        foreach($unit as $k=>$v) {
            $unit[$k]['action'] = '<button class="btn btn-sm btn-primary" type="button" onclick="updateUnit('.$v['unit_id'].',\''.$v['unit_name'].'\')"><i class="material-icons myicon-lg">edit</i></button> ';
            $unit[$k]['action'] .= '<button class="btn btn-sm btn-danger" type="button" onclick="deleteUnit('.$v['unit_id'].',\''.$v['unit_name'].'\')"><i class="material-icons myicon-lg">delete</i></button>';
        }
        $response = array("data" => $unit);
        break;

    case "get";
        $id = Sanitizer::filter('id', 'get', 'int');
        $response = $unit->getUnit($id);
        break;

    case "add";
        $name = Sanitizer::filter('unit_name', 'post');
        $unit->addUnit($name,$user_name);
        $response = array("code"=>1, "message"=>"Unit Added");
        break;

    case "update";
        $name = Sanitizer::filter('unit_name', 'post');
        $id = Sanitizer::filter('unit_id', 'post', 'int');
        $unit->updateUnit($id, $name, $user_name);
        $response = array("code"=>1, "message"=>"Unit Updated");
        break;

    case "delete";
        $id = Sanitizer::filter('unit_id', 'post', 'int');
        $unit->deleteUnit($id, $user_name);
        $response = array("code"=>1, "message"=>"Unit Deleted");
        break;
}


echo json_encode($response);

