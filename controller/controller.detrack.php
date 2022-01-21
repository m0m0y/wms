<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json");
require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "../model/model.detrack.php";

$detrack = new Detrack();
$mode = Sanitizer::filter('mode', 'get');


switch($mode) {

    case "login":
        $username = Sanitizer::filter('_', 'post');
        $password = Sanitizer::filter('__', 'post');
        $response = array("data" => array(0));
        $user_id = $detrack->login($username, $password);
        if($user_id) {
            $response = array("auth_id" => $user_id[0]['user_id']);
        }
        break;
     
    case "table";
    
        $detrack = $detrack->getAllOrdersnew();
        foreach($detrack as $k=>$v) {
            $detrack[$k]['detrack_id'] =  $v['slip_id'];
            $delivery_location = unserialize($v['delivery_location']);
            $lat = $delivery_location['lat'];
            $long = $delivery_location['long'];
            $detrack[$k]['detrack_action'] = '<button class="btn btn-sm btn-secondary" type="button" onclick="viewDetrack(\''.$lat.'\',\''.$long.'\')">view</button>';
            $detrack[$k]['detrack_type'] = 'Delivery';
            $detrack[$k]['detrack_slip'] = $v['slip_no'];
            $detrack[$k]['detrack_date'] = $v['slip_order_date'];
            $detrack[$k]['detrack_address'] = $v['customer_address'];
            $detrack[$k]['detrack_tracking'] = $v['po_no'];
            $detrack[$k]['detrack_customer'] = $v['bill_to'];
            $detrack[$k]['detrack_assignto'] = $v['user_fullname'];
            $detrack_status = "";
            if($v['tracking_status']=="Out For Delivery"){
                $detrack_status = "Not Yet Delivered";
                $detrack[$k]['detrack_trackstatus'] = '<span style="background-color:#004d99;color:white;padding:3px;border-radius:5px;font-size: 11px">'.$v['tracking_status'].'</span>';
            }
            if($v['tracking_status']=="Failed"){
                $detrack_status = "Not Delivered";
                $detrack[$k]['detrack_trackstatus'] = '<span style="background-color:#ff6666;color:white;padding:3px;border-radius:5px;font-size: 11px">'.$v['tracking_status'].'</span>';
            }
            if($v['tracking_status']=="Delivered"){
                $detrack_status = "Delivered";
                $detrack[$k]['detrack_trackstatus'] = '<span style="background-color:#79d2a0;color:white;padding:3px;border-radius:5px;font-size: 11px">'.$v['tracking_status'].'</span>';
            }
            $detrack[$k]['detrack_status'] = $detrack_status;
            $detrack[$k]['detrack_time'] = $v['time_delivered'];
            $detrack[$k]['detrack_reject'] = '0';
            $detrack[$k]['detrack_reason'] = $v['comments'];
            $detrack[$k]['detrack_receivedby'] = $v['received_by'];
            $detrack[$k]['detrack_signature'] = '<i class="material-icons myicon-lg" onclick="viewImage(\''.$v['deliver_img'].'\')">image</i>';
        }
        $response = array("data" => $detrack);
        break;
        
    case "single":
        $id = Sanitizer::filter('id', 'get');
        $response = array("data" => array());
        if($id) {
            $detrack = $detrack->getOrder($id);
            $response = array("data" => $detrack);    
        }
        break;

    case "update":
        
        $base64Image = Sanitizer::filter('image', 'post');
        $notes = Sanitizer::filter('notes', 'post');
        $orderid = (int)Sanitizer::filter('orderid', 'post');
        $receivedby = Sanitizer::filter('receivedby', 'post');
        $status = Sanitizer::filter('status', 'post');
        $filename_path = ''; // filname placeholder 
        $userid = Sanitizer::filter('userid', 'post');
        // location
        $latitude = Sanitizer::filter('latitude', 'post');
        $longitude = Sanitizer::filter('longitude', 'post');
        $location = serialize(array("lat" => $latitude, "long" => $longitude));
        // Timestamp
        $datetime = date_format(date_create(Sanitizer::filter('datetime', 'post')), 'Y-m-d H:i:s');

        // validate
        if ($status === 'delivered') {
            if(!trim($base64Image)) {
                $response = array("response"=> 0, "message" => "Delivery image is required");
                echo json_encode($response);
                exit;
            }
            if(!trim($receivedby)) {
                $response = array("response"=> 0, "message" => "Delivery Receiver is required");
                echo json_encode($response);
                exit;    
            }
        } else {
            // override optional values if status is not `delivered`
            $receivedby = $base64Image = '';
        }

        // Authenticate User
        if (!$detrack->isAllowed($userid, $orderid)) {
            $response = array("response"=> 0, "message" => "It seems that this order is not assigned to you.");
            echo json_encode($response);
            exit;  
        }

        // Convert base64 to image blob
        if ($base64Image) {
            $filename_path = md5(time().uniqid()).".jpg";
            $decoded = base64_decode($base64Image); 
            file_put_contents("../order_files/".$filename_path, $decoded);     
        }

        // Prepare status
        $tracking_status = ($status === 'delivered') ? 'Delivered' : 'Failed' ;

        if (!$detrack->updateDeliveryStatus($notes, $tracking_status, $receivedby, $filename_path, $orderid, $userid, $location, $datetime)) {
            $response = array("response"=> 0, "message" => "The server can't handle the request. Please try again later.");
            echo json_encode($response);
        }
        
        $response = array("response"=> 1, "message" => "Delivery status updated.");
        break;

    case 'userlocation':
        
        $latitude = Sanitizer::filter('latitude', 'post');
        $longitude = Sanitizer::filter('longitude', 'post');
        // Data to save
        
        $userid = Sanitizer::filter('userid', 'post');
        $timestamp = Sanitizer::filter('timestamp', 'post');
        $location = serialize(array("lat" => $latitude, "long" => $longitude));
        
        $detrack->updateLocation($userid, $location, $timestamp);
        $response = array("response"  => 1, "message"   => "Location updated.");        
        break;

    default:
        $id = Sanitizer::filter('_', 'get');
        $response = array("data" => array());
        if($id) {
            $detrack = $detrack->getUserOrders($id);
            $response = array("data" => $detrack);    
        }
        break;
}


echo json_encode($response);

