<?php


require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "controller.auth.php";
require_once "../model/model.user.php";

$auth = new Auth();
$user = new User();
$mode = Sanitizer::filter('mode', 'get');
$user_name = $auth->getSession("name");

switch($mode) {
    
    case "getall";
        $response = $user->getAllUsers();
        break;
        
    case "table";
    
        $user = $user->getAllUsers();

        foreach($user as $k=>$v) {
            $user[$k]['action'] = '<button class="btn btn-sm btn-primary" type="button" onclick="editUser('.$v['user_id'].',\''.$v['user_fullname'].'\',\''.$v['user_username'].'\',\''.$v['user_password'].'\',\''.$v['user_usertype'].'\')"><i class="material-icons myicon-lg">edit</i></button> ';
            $user[$k]['action'] .= '<button class="btn btn-sm btn-danger" type="button" onclick="deleteUser('.$v['user_id'].',\''.$v['user_username'].'\')"><i class="material-icons myicon-lg">person_remove</i></button> ';
            $user[$k]['action'] .= '<button class="btn btn-sm btn-success" type="button" onclick="barcodeUser(\''.$v['user_username'].'\',\''.$v['user_password'].'\')"><i class="material-icons myicon-lg">print</i></button>';
        }
        
        $response = array("data" => $user);
        break;

    case "get";
        $id = Sanitizer::filter('id', 'get', 'int');
        $response = $user->getUser($id);
        break;

    case "login":
        error_reporting(E_ALL);
        $username = Sanitizer::filter('username', 'post');
        $password = Sanitizer::filter('password', 'post');
        $account = $user->login($username, $password);

        if(!$account) {
            $response = array("code"=>0, "message"=>"Invalid login credentials");
            echo json_encode($response);
            exit;
        }

        $auth->setSession("auth", true);
        $auth->setSession("role", $account[2]);
        $auth->setSession("name", $account[1]);
        $auth->setSession("logid", $account[0]);

        $response = array("code"=>5, "message"=>"Welcome back ". $account[1]);
        break;

    case "add";
        $name = Sanitizer::filter('user_fullname', 'post');
        $username = Sanitizer::filter('user_username', 'post');
        $password = Sanitizer::filter('user_password', 'post');
        $usertype = Sanitizer::filter('user_usertype', 'post');
        $user->addUser($name,$username,$password,$usertype,$user_name);
        $response = array("code"=>1, "message"=>"User Added");
        break;

    case "update";
        $name = Sanitizer::filter('user_fullname', 'post');
        $username = Sanitizer::filter('user_username', 'post');
        $password = Sanitizer::filter('user_password', 'post');
        $usertype = Sanitizer::filter('user_usertype', 'post');
        $id = Sanitizer::filter('user_id', 'post', 'int');
        $user->updateUser($id, $name,$username,$password,$usertype,$user_name);
        $response = array("code"=>1, "message"=>"User Updated");
        break;

    case "delete";
        $id = Sanitizer::filter('user_id', 'post', 'int');
        $user->deleteUser($id,$user_name);
        $response = array("code"=>1, "message"=>"User Deleted");
        break;
}


echo json_encode($response);

