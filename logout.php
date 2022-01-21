<?php

require_once "controller/controller.auth.php";
$auth = new Auth();

$auth->sessionDie("login.php");
