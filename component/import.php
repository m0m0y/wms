<?php

date_default_timezone_set('Asia/Manila');
error_reporting(E_ALL);
ini_set('display_errors', 'Off');
ini_set("log_errors", 1);
ini_set('ignore_repeated_errors', TRUE);
ini_set('error_log', './log/error.log');

require_once "./controller/controller.auth.php";
require_once "./controller/controller.sanitizer.php";
require_once "./controller/controller.db.php";

$auth = new Auth();
$isLoggedIn = $auth->getSession("auth");

$auth->redirect("auth", true, "login.php");


$__role = $auth->getSession("role");