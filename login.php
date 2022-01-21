<?php

date_default_timezone_set("Asia/Manila");

require_once "./controller/controller.auth.php";
require_once "./controller/controller.sanitizer.php";
require_once "./controller/controller.db.php";

$meta_title = 'Login - Warehouse Management System';
$auth = new Auth();
$isLoggedIn = $auth->getSession("auth");
$auth->redirect("auth", false, "index.php");


require_once "./component/header.php";
?>
<script src="/wms/lib/jquery/scanner.js"></script>
<script src="/wms/static/js/ajax.js"></script>
<link rel="stylesheet" href="/wms/static/css/login.css?v=21">

<div class="page">
    <div class="login-wrapper" id="login_option">
        <form class="ajax-form mx-auto d-block">
            
            <h1 class="mb-2 text-left">Welcome to WMS</h1>
            <h1 class="mb-5 text-left text-muted m-0"><small>Select a login option below.</small></h1>
            <button type="button" onclick="scan_login()" class="btn btn-lg btn-success l d-block w-100 mt-4"><small>Scan</small></button>
            <button type="button" onclick="input_login()" class="btn btn-lg btn-success l d-block w-100 mt-4"><small>Input</small></button>
        </form>
        
        <span class="foot-notes">
            <label class="text-muted">&copy; <?= date('Y') ?> PMC Group of companies - WMS v1.0</label>
        </span>
    </div>
    <div class="login-wrapper" id="login_input" style="display:none">
        <form action="controller/controller.user.php?mode=login" method="POST" class="ajax-form mx-auto d-block">
            
            <!-- 
            <img src="static/css/font/logo.png" alt="Progressive Medical Corporation" class="d-block mt-3 mb-5 mx-auto" />
            -->
            <h1 class="mb-2 text-left">Log in</h1>
            <h1 class="mb-5 text-left text-muted m-0"><small>Sign into your account</small></h1>
            <input type="text" class="form-control mb-4 rounded-0" name="username" placeholder="user@progressive" required />
            <input type="password" class="form-control mb-4 rounded-0" name="password" placeholder="Enter your password" required />
            <button type="submit" class="btn btn-lg btn-primary d-block w-100 mt-4"><small>Login</small></button>
            <a href="" class="d-block w-100 mt-4">Back to login options</a>
        </form>
        
        <span class="foot-notes">
            <label class="text-muted">&copy; <?= date('Y') ?> PMC Group of companies - WMS v1.0</label>
        </span>
    </div>
</div>

<div id="validity-modal" class="modal fade" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <div id="validity-success" class="modal-body text-center">

                <div id="choose-cart" class="text-left">
                    <div class="icon-lg-pop my-4 text-center">
                        <i class="material-icons">qr_code</i>
                    </div>
                    <p class="mb-5 text-center mb-2"><b>Almost there!</b><br>Please scan your <b class="text-primary">ID</b> now</p>
                    <input type="hidden" class="form-control d-inline" id="pickingCart">
                </div>
                
            </div>
        </div>
    </div>
</div>


<?php
require_once "./component/footer.php";
