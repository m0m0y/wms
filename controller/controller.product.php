<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "controller.auth.php";
require_once "controller.filevalidator.php";
require_once "../model/model.product.php";

$product = new Product();
$auth = new Auth();
$mode = Sanitizer::filter('mode', 'get');
$user_name = $auth->getSession("name");


switch($mode) {
    
    case "getall";

        $response = $product->getAllProducts();
        break;
        
    case "table";
        
        $product = $product->getAllProducts();
        foreach($product as $k=>$v) {
            $product[$k]['action'] = '<button class="btn btn-sm btn-primary" type="button" onclick="editproduct('.$v['product_id'].',\''.$v['category_id'].'\',\''.$v['unit_id'].'\',\''.$v['product_type'].'\',\''.$v['product_code'].'\',\''.$v['product_description'].'\',\''.$v['product_weight'].'\',\''.$v['product_length'].'\',\''.$v['product_width'].'\',\''.$v['product_height'].'\',\''.$v['product_expiration'].'\')"><i class="material-icons myicon-lg">edit</i></button> ';
            $product[$k]['action'] .= '<button class="btn btn-sm btn-danger" type="button" onclick="deleteproduct('.$v['product_id'].',\''.$v['product_description'].'\',\''.$v['product_code'].'\')"><i class="material-icons myicon-lg">delete</i></button>';
        }
        $response = array("data" => $product);
        break;

    case "get";
    
        $id = Sanitizer::filter('id', 'get', 'int');
        $lot_id = Sanitizer::filter('lot_id', 'get', 'int');
        $response = $product->getProduct($id,$lot_id);
        break;

    case "add";

        $category_id = Sanitizer::filter('category_id', 'post');
        $unit_id = Sanitizer::filter('unit_id', 'post');
        $product_type = Sanitizer::filter('product_type', 'post');
        $product_code = Sanitizer::filter('product_code', 'post');
        $product_description = Sanitizer::filter('product_description', 'post');
        $product_expiration = Sanitizer::filter('product_expiration', 'post');

        $product_weight = Sanitizer::filter('product_weight', 'post');
        $product_length = Sanitizer::filter('product_length', 'post');
        $product_width = Sanitizer::filter('product_width', 'post');
        $product_height = Sanitizer::filter('product_height', 'post');

        $product_weight = number_format($product_weight, '4', '.', ',');
        $product_length = number_format($product_length, '4', '.', ',');
        $product_width = number_format($product_width, '4', '.', ',');
        $product_height = number_format($product_height, '4', '.', ',');

        $product->addProduct($category_id,$unit_id,$product_type,$product_code,$product_description,$product_weight,$product_length,$product_width,$product_height,$product_expiration,$user_name);
        


            if (($_FILES['product_image']['name']!="")){

                if(!FileValidator::allowedSize('product_image', '1000000000') || !FileValidator::allowedType('product_image', array('jpg', 'JPG', 'jpeg'))) {
                    echo json_encode(array('code'=>0,'message'=>'Invalid File.'));
                    die(); 
                }

                $target_dir = "../product_image/";
                $file = $_FILES['product_image']['name'];
                $path = pathinfo($file);
                $filename = $product_code;
                $ext = $path['extension'];
                $attachfile = $filename.".".$ext;
                $temp_name = $_FILES['product_image']['tmp_name'];
                $path_filename_ext = $target_dir.$filename.".".$ext;

                if (file_exists($path_filename_ext)) {
                    
                    $response = array('code'=>0,'message'=>'Upload failed. File already exists.');

                } else {
                    move_uploaded_file($temp_name,$path_filename_ext);
                    
                }
                
                $response = array("code"=>1, "message"=>"Product Added");
            
            } else {

                $response = array('code'=>0,'message'=>'file upload failed');
            }

        break;

    case "update";
        $category_id = Sanitizer::filter('category_id', 'post');
        $unit_id = Sanitizer::filter('unit_id', 'post');
        $product_type = Sanitizer::filter('product_type', 'post');
        $product_code = Sanitizer::filter('product_code', 'post');
        $product_description = Sanitizer::filter('product_description', 'post');
        $product_expiration = Sanitizer::filter('product_expiration', 'post');
        $product_id = Sanitizer::filter('product_id', 'post');

        $product_weight = Sanitizer::filter('product_weight', 'post');
        $product_length = Sanitizer::filter('product_length', 'post');
        $product_width = Sanitizer::filter('product_width', 'post');
        $product_height = Sanitizer::filter('product_height', 'post');

        $product_weight = number_format($product_weight, '4', '.', ',');
        $product_length = number_format($product_length, '4', '.', ',');
        $product_width = number_format($product_width, '4', '.', ',');
        $product_height = number_format($product_height, '4', '.', ',');

        $product->updateProduct($category_id,$unit_id,$product_type,$product_code,$product_description,$product_weight,$product_length,$product_width,$product_height,$product_expiration,$product_id,$user_name);
        
        $response = array("code"=>1, "message"=>"Product Updated");

        if (($_FILES['product_image']['name']!="")){

            if(!FileValidator::allowedSize('product_image', '1000000000') || !FileValidator::allowedType('product_image', array('jpg'))) {
                echo json_encode(array('code'=>0,'message'=>'Invalid File.'));
                die(); 
            }

            $target_dir = "../product_image/";
            $file = $_FILES['product_image']['name'];
            $path = pathinfo($file);
            $filename = $product_code;
            $ext = $path['extension'];
            $attachfile = $filename.".".$ext;
            $temp_name = $_FILES['product_image']['tmp_name'];
            $path_filename_ext = $target_dir.$filename.".".$ext;

            if (file_exists($path_filename_ext)) { unlink($path_filename_ext); }

            if (!move_uploaded_file($temp_name,$path_filename_ext)) {
                $response = array("code"=>1, "message"=>"Product Updated, but image was not saved.");
            };
        }

        break;

    case "delete";
        $id = Sanitizer::filter('product_id', 'post', 'int');
        $product_code = Sanitizer::filter('del_product_code','post');
        $product->deleteProduct($id,$user_name);
        $target_dir = "../product_image/";
        $filename = $product_code;
        $ext = "jpg";
        $path_filename_ext = $target_dir.$filename.".".$ext;
        if(file_exists($path_filename_ext)){
            unlink($path_filename_ext);
        }

        
        $response = array("code"=>1, "message"=>"Product Deleted");
        break;
}


echo json_encode($response);

