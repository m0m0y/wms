<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?= isset($meta_title) ? $meta_title : '404' ?></title>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta name="format-detection" content="telephone=no">
        <link rel="icon" type="image/png" href="/wms/static/ico.png">
        <link rel="stylesheet" href="/wms/lib/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="/wms/lib/toast/toast.min.css">
        <link rel="stylesheet" href="/wms/static/css/font.css?v=2">
        <link rel="stylesheet" href="/wms/static/css/main.css?v=2">
        <link rel="stylesheet" href="/wms/static/css/sidebar.css?v=2">
        <link rel="stylesheet" href="/wms/static/css/inmed.css?v=2">

        <?php if(isset($_COOKIE['wms-dark'])) { ?>
        <link id="dark-css" rel="stylesheet" href="/wms/static/css/dark.css?v=2">
        <?php } ?>
        
        <!-- <link rel="stylesheet" href="/wms/lib/simplebar/simplebar.css?v=2"> -->
        <link rel="stylesheet" href="/wms/lib/ripple/ripple.min.css?v=2">
        
        <script src="/wms/lib/jquery/jquery.js"></script>        
        <script src="/wms/lib/popper/popper.min.js"></script>    
        
        <script src="/wms/lib/ripple/ripple.min.js"></script>
        <!-- <script src="/wms/lib/simplebar/simplebar.js"></script> -->
        
        <script src="/wms/lib/bootstrap/js/bootstrap.min.js"></script>
        <script src="/wms/lib/toast/toast.min.js"></script>        
        <script src="/wms/static/js/main.js?v=62"></script>

        <link rel="stylesheet" type="text/css" href="lib/static/master/master.css?v=2">
        <script src="lib/static/master/master.js?v=2"></script>
        <script src="/wms/static/js/electron.js?v=65"></script>
        
    </head>
    <body class="<?= (isset($_COOKIE['side-off'])) ? 'off no-transition' : '' ?>">

    <div id="main-loader">
        <div>
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>

    <div class="i-loader"><div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>