<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "controller.auth.php";
require_once "../model/model.category.php";

$auth = new Auth();
$category = new Category();
$mode = Sanitizer::filter('mode', 'get');
$user_name = $auth->getSession("name");


switch($mode) {
    
    case "getall";
        $response = $category->getAllCategory();
        break;
    case "option";
        $category = $category->getAllCategory();
        $html = "";
        
        foreach($category as $k=>$v){
            $id = $category[$k]["category_id"];
            $name = $category[$k]["category_name"];
            $html .= "<option value='$id'>$name</option>";
        }

        $response = array("code"=>1,"html"=>$html);
        break;
        
    case "table";
    
        $category = $category->getAllCategory();
        foreach($category as $k=>$v) {
            $category[$k]['action'] = '<button class="btn btn-sm btn-primary" type="button" onclick="editCategory('.$v['category_id'].',\''.$v['category_name'].'\')"><i class="material-icons myicon-lg">edit</i></button> ';
            $category[$k]['action'] .= '<button class="btn btn-sm btn-danger" type="button" onclick="deleteCategory('.$v['category_id'].',\''.$v['category_name'].'\')"><i class="material-icons myicon-lg">delete</i></button>';
        }
        $response = array("data" => $category);
        break;

    case "get";
        $id = Sanitizer::filter('id', 'get', 'int');
        $response = $category->getCategory($id);
        break;

    case "add";
        $name = Sanitizer::filter('category_name', 'post');
        $category->addCategory($name,$user_name);
        $response = array("code"=>1, "message"=>"Category Added");
        break;

    case "update";
        $name = Sanitizer::filter('category_name', 'post');
        $id = Sanitizer::filter('category_id', 'post', 'int');
        $category->updateCategory($id, $name,$user_name);
        $response = array("code"=>1, "message"=>"Category Updated");
        break;

    case "delete";
        $id = Sanitizer::filter('category_id', 'post', 'int');
        $category->deleteCategory($id,$user_name);
        $response = array("code"=>1, "message"=>"Category Deleted");
        break;
}


echo json_encode($response);

