<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "../model/model.customer.php";

$customer = new Customer();
$mode = Sanitizer::filter('mode', 'get');


switch($mode) {
    
    case "getall";
        $response = $customer->getAllCustomers();
        break;
        
    case "table";
    
        $customer = $customer->getAllCustomers();

        foreach($customer as $k=>$v) {
            $customer[$k]['action'] = '<button class="btn btn-sm btn-primary" type="button" onclick="editCustomer('.$v['customer_id'].',\''.$v['customer_name'].'\',\''.$v['customer_contactno'].'\',\''.$v['customer_address'].'\')">edit</button> ';
            $customer[$k]['action'] .= '<button class="btn btn-sm btn-danger" type="button" onclick="deleteCustomer('.$v['customer_id'].',\''.$v['customer_name'].'\')">delete</button>';
        }
        
        $response = array("data" => $customer);
        break;

    case "get";
        $id = Sanitizer::filter('id', 'get', 'int');
        $response = $customer->getCustomer($id);
        break;

    case "add";
        $name = Sanitizer::filter('customer_name', 'post');
        $contactno = Sanitizer::filter('customer_contactno', 'post');
        $address = Sanitizer::filter('customer_address', 'post');
        $customer->addCustomer($name,$contactno,$address);
        $response = array("code"=>1, "message"=>"Customer Added");
        break;

    case "update";
        $name = Sanitizer::filter('customer_name', 'post');
        $contactno = Sanitizer::filter('customer_contactno', 'post');
        $address = Sanitizer::filter('customer_address', 'post');
        $id = Sanitizer::filter('customer_id', 'post', 'int');
        $customer->updateCustomer($id, $name,$contactno,$address);
        $response = array("code"=>1, "message"=>"Customer Updated");
        break;

    case "delete";
        $id = Sanitizer::filter('customer_id', 'post', 'int');
        $customer->deleteCustomer($id);
        $response = array("code"=>1, "message"=>"Customer Deleted");
        break;
}


echo json_encode($response);

