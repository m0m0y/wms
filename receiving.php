<?php

require_once "./component/import.php";
$meta_title = 'Receiving - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";
require_once "model/model.receiving.php";

$receiving = new Receiving();

?>

<script src="/wms/lib/jquery/scanner.js"></script>
<script src="/wms/services/picking/receiving.js?v=beta-11"></script>

<audio id="audio_correct">
  <source src="barcode_sounds/beep_correct.mp3" type="audio/mpeg">
</audio>

<audio id="audio_incorrect">
  <source src="barcode_sounds/beep_incorrect.mp3" type="audio/mpeg">
</audio>

<div class="user-picking" id="user-picking">
    <!-- ajax -->
    
</div>

<div class="main-content" id="live">
    <div class="row row-cols-1" id="product-set">
        <div class="col">
            <div class="padded mb-5">
                <h1 class="mt-5"><i class="material-icons mr-3">call_received</i> Receiving</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">Receiving</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 padded">
        <?php
            $reports = $receiving->getAllReports(true);
            if(!empty($reports)){
                foreach($reports as $key => $v){
                    ?>
                        <div class="col">
                            <div class="card-panel p-4 pick-report" data-id="<?= $v['report_id'] ?>">
                                <p class="m-0"><b>#<?= $v['control_no'] ?></b> &mdash; <small class="m-0 text-muted"><?= date('D M j Y', strtotime($v['delivery'])) ?></small></p>
                                <p class="m-0 mb-2 font-weight-bold"><small class="font-weight-normal px-2 mr-2 bg-info text-white"><?= $v['company_name'] ?></small></p>
                                
                            </div>
                        </div>
                    <?php
                }
            } else {
                ?>
                <div class="col">
                    <div class="card-panel p-4 border-0 empty rounded-lg">
                        <p class="m-0 text-muted"><small><?= date("jS \of M Y") ?></small></p>
                        <p class="m-0 font-weight-bold">There are no items to receive</p>
                    </div>
                </div>
                <?php
            }
        ?>
    </div>
</div>

<div id="validity-fail" class="modal fade" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <div class="modal-body p-5">

                <div class="card-panel p-4 border-0 mb-0 rounded-0 text-cente" data-dismiss="modal">
                    <div class="icon-lg-pop mb-4 text-center">
                        <i class="material-icons text-danger">location_off</i>
                    </div>
                    <p class="m-0 font-weight-bold text-center">Wrong Item</p>
                    <p class="m-0 font-weight-bold text-muted text-center">It seems like you are attempting to count the wrong Item.</p>
                </div>

            </div>
        </div>
    </div>
</div>

<div id="validity-modal" class="modal fade" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <div id="validity-success" class="modal-body text-center">
                <div id="choose-qty">
                    <div class="icon-lg-pop my-4 text-center">
                        <i class="material-icons text-success">done_all</i>
                    </div>
                    <p class="mb-2"><b>Item Verified</b><br>Please enter quantity</p>
                    <div class="input-group mb-3 control-group">
                        <div class="input-group-append">
                            <button class="pick-control input-group-text" data-type="-" data-target="#pickingQuantity">-</button>
                        </div>
                        <input type="number" id="pickingQuantity" class="form-control" value="1" style="pointer-events: all;" autocomplete="off">
                        <div class="input-group-prepend">
                            <button class="pick-control input-group-text" data-type="+" data-target="#pickingQuantity">+</button>
                        </div>
                    </div>
                    <button class="btn btn-success mb-3 control-group-submit" onclick="receive()">Add to total</button> 
                </div>
                
            </div>
        </div>
    </div>
</div>

<div id="edit-modal" class="modal fade" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <div class="modal-body text-center">
                <div>
                    <div class="icon-lg-pop my-4 text-center">
                        <i class="material-icons text-muted">refresh</i>
                    </div>
                    <p class="mb-2"><b>Edit Count</b><br>Please enter quantity</p>
                    <div class="input-group mb-3 control-group">
                        <div class="input-group-append">
                            <button class="pick-control input-group-text" data-type="-" data-target="#editQty">-</button>
                        </div>
                        <input type="number" id="editQty" class="form-control" value="1" style="pointer-events: all;" autocomplete="off">
                        <div class="input-group-prepend">
                            <button class="pick-control input-group-text" data-type="+" data-target="#editQty">+</button>
                        </div>
                    </div>
                    <button class="btn btn-primary mb-3 control-group-submit" onclick="updateCount()">Update Count</button> 
                </div>
                
            </div>
        </div>
    </div>
</div>
