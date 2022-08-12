<?php

require_once "./component/import.php";
$meta_title = 'Dashboard - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";

require_once "./model/model.dashboard.php";

date_default_timezone_set("Asia/Manila");

$dashboard = new Dashboard();

$order = $dashboard->getAllPicking();
$invoices = $dashboard->getAllInvoice();
$checked = $dashboard->getAllChecking();
$packed = $dashboard->getAllPacking();
$shipping = $dashboard->getAllShipping();

?>

<div class="main-content" id="live">

    <div class="row row-cols-1">
        <div class="col">
            <div class="padded mb-5">
                
                <h1 class="mt-5"><i class="material-icons mr-3">insights</i> Dashboard</h1>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Last Updated</li>
                        <li class="breadcrumb-item active" aria-current="page"><?= date('g:ia M j Y') ?></li>
                    </ol>
                </nav>

            </div>
        </div>  
    </div>

    <div class="row row-cols-1 row-cols-sm-2 padded">
    <?php
    if(!empty($order)){
    foreach($order as $k=>$v) {
        $slip_id = $v['slip_id'];
        $slip_no = $v['slip_no'];
        $customer_name = ucfirst($v['ship_to']);
        $pick_percentage = ($v['total_picked']) ? number_format(($v['total_picked']/$v['total_qty']) * 100, "1", ".", ",") : 0;
        $repick = ($v['order_status']=="repick") ? 'border-dashed border border-warning' : '';
        $status = ($v['order_status']=="repick") ? 'repick' : 'picking';
        $user_name = $v['user_username'];
        $fullname = $v['user_fullname'];
    ?>
    <div class="col">
        
        <div class="card-panel p-4 <?= $repick ?>">
            <p class="m-0 text-muted"><small><?= date("jS \of M Y", strtotime($v['slip_order_date'])) ?></small></p>
            <p class="m-0 mb-2 font-weight-bold">
                <small class="font-weight-normal px-2 mr-2 bg-warning text-white"><?= $status ?></small>
                <?= $slip_no ?>
                <small class="font-weight-normal px-2 mt-1 bg-muted text-white float-right d-inline">@ <?= $fullname ?></small>
            </p>
            <div class="progress mb-3 rounded-0" style="height: 10px;">
                <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $pick_percentage ?>%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>
    <?php 
        } 
    }
    if(!empty($invoices)){
    foreach($invoices as $k=>$v) {
        $slip_id = $v['slip_id'];
        $slip_no = $v['slip_no'];
        $invoice = ($v['invoice_no']!="") ? '<small class="font-weight-normal px-2 mt-1 bg-muted text-black float-right d-inline">Inv No: '.$v['invoice_no'].'</small>' : '';
        $customer_name = ucfirst($v['ship_to']);
        $pick_percentage = ($v['total_picked']) ? number_format(($v['total_picked']/$v['total_qty']) * 100, "1", ".", ",") : 0;
        $repick = ($v['order_status']=="repick") ? 'border border-warning' : '';
    ?>
    <div class="col">
        <div class="card-panel p-4 <?= $repick ?>">
            <p class="m-0 text-muted"><small><?= date("jS \of M Y", strtotime($v['slip_order_date'])) ?></small></p>
            <p class="m-0 mb-2 font-weight-bold"><small class="font-weight-normal px-2 mr-2 bg-info text-white">invoicing</small><?= $slip_no ?> <?= $invoice ?></p>
            <div class="progress mb-3 rounded-0" style="height: 10px;">
                <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>
    <?php 
        } 
    }
    if(!empty($checked)){
        foreach($checked as $k=>$v) {
            $slip_id = $v['slip_id'];
            $slip_no = $v['slip_no'];
            $invoice = ($v['invoice_no']!="") ? '<small class="font-weight-normal px-2 mt-1 bg-muted text-black float-right d-inline">Inv No: '.$v['invoice_no'].'</small>' : '';
            $customer_name = ucfirst($v['ship_to']);
            $pick_percentage = ($v['total_picked']) ? number_format(($v['total_picked']/$v['total_qty']) * 100, "1", ".", ",") : 0;
            $repick = ($v['order_status']=="repick") ? 'border border-warning' : '';
        ?>
        <div class="col">
            <div class="card-panel p-4 <?= $repick ?>">
                <p class="m-0 text-muted"><small><?= date("jS \of M Y", strtotime($v['slip_order_date'])) ?></small></p>
                <p class="m-0 mb-2 font-weight-bold"><small class="font-weight-normal px-2 mr-2 bg-primary text-white">checking</small><?= $slip_no ?> <?= $invoice ?></p>
                <div class="progress mb-3 rounded-0" style="height: 10px;">
                    <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $pick_percentage ?>%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

        </div>
    <?php 
        } 
    }
    if(!empty($packed)){

        foreach($packed as $k=>$v) {
            $slip_id = $v['slip_id'];
            $slip_no = $v['slip_no'];
            $invoice = ($v['invoice_no']!="") ? '<small class="font-weight-normal px-2 mt-1 bg-muted text-black float-right d-inline">Inv No: '.$v['invoice_no'].'</small>' : '';
            $customer_name = ucfirst($v['ship_to']);
            $pick_percentage = ($v['total_picked']) ? number_format(($v['total_picked']/$v['total_qty']) * 100, "1", ".", ",") : 0;
            
            $repick = ($v['order_status']=="repick") ? 'border border-warning' : '';
        ?>
        <div class="col">
            <div class="card-panel p-4 <?= $repick ?>">
                <p class="m-0 text-muted"><small><?= date("jS \of M Y", strtotime($v['slip_order_date'])) ?></small></p>
                <p class="m-0 mb-2 font-weight-bold"><small class="font-weight-normal px-2 mr-2 bg-success text-white">packing</small><?= $slip_no ?> <?= $invoice ?></p>
                <div class="progress mb-3 rounded-0" style="height: 10px;">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $pick_percentage ?>%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

        </div>
    <?php 
        } 
    }
    if(!empty($shipping)){
        foreach($shipping as $k=>$v) {
            $slip_id = $v['slip_id'];
            $slip_no = $v['slip_no'];
            $invoice = ($v['invoice_no']!="") ? '<small class="font-weight-normal px-2 mt-1 bg-muted text-black float-right d-inline">Inv No: '.$v['invoice_no'].'</small>' : '';
            $customer_name = ucfirst($v['ship_to']);
            $pick_percentage = ($v['total_picked']) ? number_format(($v['total_picked']/$v['total_qty']) * 100, "1", ".", ",") : 0;
            $repick = ($v['order_status']=="repick") ? 'border border-warning' : '';
        ?>
        <div class="col">
            <div class="card-panel p-4 <?= $repick ?>">
                <p class="m-0 text-muted"><small><?= date("jS \of M Y", strtotime($v['slip_order_date'])) ?></small></p>
                <p class="m-0 mb-2 font-weight-bold"><small class="font-weight-normal px-2 mr-2 bg-info text-white">shipping</small><?= $slip_no ?> <?= $invoice ?></p>
                <div class="progress mb-3 rounded-0" style="height: 10px;">
                    <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" role="progressbar" style="width: 80%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
        <?php 
            } 
        }

    if(empty($order) && empty($invoices) && empty($checked) && empty($packed) && empty($shipping) ){ ?>

        <div class="col">
            <div class="card-panel p-4 border-0 empty rounded-lg">
                <p class="m-0 text-muted"><small>Welcome to your dashboard</small></p>
                <p class="m-0 font-weight-bold">The system don't have pending transactions right now.</p>
            </div>
        </div>

    <?php } ?>

    </div>

</div>

<script>

$(function(){
    autoUpdate();
    /* $(document).on('keydown', function(){
        theater();
    }) */
    /* 
    $(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange MSFullscreenChange', function(){
        theater()
    });
    */
})

function autoUpdate(){
    setInterval(function(){
        $("#live").load(location.href + " #live>*", "");
    }, 5000)
}

function theater(){
    $('.sidebar').hide();
    $('body').css({"padding-left" : "0"})
    document.documentElement.webkitRequestFullScreen();
}


</script>

<?php
require_once "./component/footer.php";
