<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "controller.auth.php";
require_once "../model/model.rak.php";

$auth = new Auth();
$rak = new Rak();
$mode = Sanitizer::filter('mode', 'get');
$user_name = $auth->getSession("name");


switch($mode) {
    
    case "getall";
        $response = $rak->getAllRaks();
        break;
        
    case "table";
    
        $rak = $rak->getAllRaks();

        foreach($rak as $k=>$v) {
            $lotno = $v['rak_id'].'**'.$v['rak_name'].'-'.$v['rak_column'].'-'.$v['rak_level'];
            $rak[$k]['rak_labelname'] = $v['rak_name'].'-'.$v['rak_column'].'-'.$v['rak_level'];
            $rak[$k]['action'] = '<button class="btn btn-sm btn-primary" type="button" onclick="editRak('.$v['rak_id'].',\''.$v['rak_name'].'\',\''.$v['rak_column'].'\',\''.$v['rak_level'].'\')"><i class="material-icons myicon-lg">edit</i></button> ';
            $rak[$k]['action'] .= '<button class="btn btn-sm btn-danger" type="button" onclick="deleteRak('.$v['rak_id'].',\''.$v['rak_name'].'\')"><i class="material-icons myicon-lg">delete</i></button> ';
            $rak[$k]['action'] .= '<a class="btn btn-sm btn-success" target="_blank" href="tcpdf/examples/barcode.php?stock_lotno='.$lotno.'"><i class="material-icons myicon-lg">print</i></a>';
        
        }
        
        $response = array("data" => $rak);
        break;

    case "get";
        $id = Sanitizer::filter('id', 'get', 'int');
        $response = $rak->getRak($id);
        break;

    case "add";
        $name = Sanitizer::filter('rak_name', 'post');
        $column = Sanitizer::filter('rak_column', 'post');
        $level = Sanitizer::filter('rak_level', 'post');
        $rak->addRak($name,$column,$level,$user_name);
        $response = array("code"=>1, "message"=>"Rak Added");
        break;

    case "update";
        $name = Sanitizer::filter('rak_name', 'post');
        $column = Sanitizer::filter('rak_column', 'post');
        $level = Sanitizer::filter('rak_level', 'post');
        $id = Sanitizer::filter('rak_id', 'post', 'int');
        $rak->updateRak($id, $name,$column,$level,$user_name);
        $response = array("code"=>1, "message"=>"Rak Updated");
        break;

    case "delete";
        $id = Sanitizer::filter('rak_id', 'post', 'int');
        $rak->deleteRak($id,$user_name);
        $response = array("code"=>1, "message"=>"Rak Deleted");
        break;
}


echo json_encode($response);

