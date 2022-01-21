<?php

require_once "./component/import.php";
$meta_title = 'Transfering - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";
require_once "model/model.transfering.php";

$transfering = new Transfering();

?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/lib/jquery/scanner.js"></script>

<script src="/wms/services/transfer/transfering.js?v=lsorem"></script>

<audio id="audio_correct">
  <source src="barcode_sounds/beep_correct.mp3" type="audio/mpeg">
</audio>

<audio id="audio_incorrect">
  <source src="barcode_sounds/beep_incorrect.mp3" type="audio/mpeg">
</audio>

<div class="user-picking p-0" id="user-picking">
    <!-- ajax -->
</div>


<div class="main-content">
    <div class="row row-cols-1" id="product-set">
        <div class="col">
            <div class="padded mb-5">
                
                <h1 class="mt-5"><i class="material-icons mr-3">transfer_within_a_station</i> Transfering</h1>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">Transfering</li>
                    </ol>
                </nav>

                <div class="input-group my-4">
                    <div class="input-group-prepend">
                        <a class="btn btn-primary rounded-0 add-field" type="button" id="btn_transfer"><i class="material-icons myicon-lg">add</i> Transfer Request</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3">
        <?php
        
        $transfer = $transfering->getAllTransfering();

        if(!empty($transfer)){
        foreach($transfer as $k=>$v) {
            $transfer_id = $v['id'];
            $stock_id = $v['stock_id'];
            $zero = 0;
            $quantity_moved = ($v['transfer_status']=="Approve") ? $zero : $v['stock_qty_moving'];
            $rak_id = $v['rak_id'];
            $pick_percentage = ($quantity_moved) ? number_format(($v['stock_qty_moving'] / $quantity_moved) * 100, "1", ".", ",") : 0;
            $transfer_status = ($v['transfer_status']=="repick") ? 'border border-warning' : '';
        ?>
        <div class="col">
            <div class="padded">
                <div class="card-panel p-4 <?= $transfer_status ?>" onclick="pickOrder('<?= $transfer_id ?>', '<?= $rak_id ?>', '<?= $stock_id ?>')">
                    <p class="m-0 text-muted"><small><?= date("jS \of M Y", strtotime($v['date_request'])) ?></small></p>
                    <p class="m-0"><?= $v['product_description'] ?></p>

                    <?php if ($v['transfer_status']=="repick"): ?>
                    <p class="comment"><?php echo $v['transfer_status']; ?></p>
                    <?php endif ?>

                    <div class="progress mb-3 rounded-0 mt-3" style="height: 10px;">
                        <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $pick_percentage ?>%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>

        </div>
        <?php 
        } } else {
        ?>

        <div class="col">
            <div class="padded mb-5">
                <div class="card-panel p-4 border-0 empty rounded-lg">
                    <p class="m-0 text-muted"><small><?= date("jS \of M Y") ?></small></p>
                    <p class="m-0 font-weight-bold">There are no request at this moment.</p>
                </div>
            </div>
        </div>

        <?php
        }
        ?>
    </div>
</div>

<input type="hidden" id="viewcache_id" />
<input type="hidden" id="viewcache_name" />
<input type="hidden" id="viewcache_no" />

<div id="scan_lot" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body p-5">
                <div class="card-panel p-4 border-0 mb-0 rounded-lg">
                    <div class="icon-lg-pop my-4 text-center">
                        <i class="material-icons text-muted">qr_code</i>
                    </div>
                    <p class="m-0 text-center font-weight-bold">Picking Item</p>
                    <p class="m-0 font-weight-bold text-muted text-center mb-2">Please scan item with lot number: <span id="lot-to-scan"></span></p>
                    <p class="m-0 font-weight-bold text-muted text-center mb-2" id="lot-to-scan"><!-- note --></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="validity-modal" class="modal fade" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="validity-success" class="modal-body text-center">
                <div id="choose-qty">
                    
                    <div class="icon-lg-pop my-4 text-center">
                        <i class="material-icons text-success">done_all</i>
                    </div>
                    
                    <p id="pickingForm" class="d-none">
                        <input type="hidden" id="order_details_id" name="" />
                        <input type="hidden" id="stock_id" name="" />
                        <input type="hidden" id="product_id" name=""  />
                        <input type="hidden" id="stock_lotno" name="" />
                        <input type="hidden" id="stock_serialno" name="" />
                        <input type="hidden" id="stock_qty" name="" />
                        <input type="hidden" id="stock_expiration_date" name="" />
                    </p>
                    
                    <p class="mb-2"><b>Lot Number Verified</b><br>Confirm item to be picked</p>


                    <input type="hidden" id="moving_to_rak" class="form-control">

                    <div class="input-group mb-3 control-group">
                        <div class="input-group-append">
                            <button class="pick-control input-group-text" data-type="?" data-target="#pickingQuantity">-</button>
                        </div>
                        <input type="number" id="pickingQuantity" class="form-control">
                        <div class="input-group-prepend">
                            <button class="pick-control input-group-text" data-type="?" data-target="#pickingQuantity">+</span>
                        </div>
                    </div>
                    <button class="btn btn-success mb-3 control-group-submit d-block" onclick="chooseRak()">Pick Item</button>


                </div>
                <div id="choose-cart" style="display: none;">
                    <div class="icon-lg-pop my-4 text-center">
                        <i class="material-icons text-muted">qr_code</i>
                    </div>
                    <p class="mb-4"><b>Almost there!</b><br>Please scan RAK now</p>
                    <input type="hidden" class="form-control d-inline" id="pickingRak">
                </div>
                
            </div>
        </div>
    </div>
</div>


<div id="transfer_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body p-5">
                <div class="card-panel p-4 border-0 mb-0 rounded-lg">
                    
                    <div class="icon-lg-pop my-4 text-center">
                        <i class="material-icons text-success">done_all</i>
                    </div>

                    <p class="m-0 text-center font-weight-bold">Transfer Request</p>
                    <p class="m-0 font-weight-bold text-muted text-center mb-2">Please scan the item's barcode that you would like to change location.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="transfer_form" class="modal fade">
    <div class="modal-dialog">
        <form action="controller/controller.transfering.php?mode=addRequest" method="POST" class="ajax-form" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card-panel p-4 border-0 mb-0 rounded-lg">
                        <p class="m-0 font-weight-bold">Location to get</p>
                        <select id="stock-id" name="stock_id" class="form-control my-2 mb-3"></select>
                        <div id="stock-count">
                            <p class="m-0 font-weight-bold">Quantity to move</p>
                            
                            <div class="input-group mt-2 mb-3 control-group full">
                                <div class="input-group-prepend">
                                    <button type="button" class="pick-control input-group-text" data-type="-" data-target="#qty-stock">-</button>
                                </div>
                                <input type="number" name="quantity_stock" id="qty-stock" class="form-control free">
                                <div class="input-group-append">
                                    <button type="button" class="pick-control input-group-text" data-type="+" data-target="#qty-stock">+</button>
                                </div>
                            </div>
                            
                            <p class="m-0 font-weight-bold">Move to</p>
                            <select id="location-id" name="rak_id" class="form-control my-2 mb-4"></select>
                            <button type="submit" class="btn btn-primary">Request Transfer</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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
                    <p class="m-0 font-weight-bold text-center">Wrong lot number</p>
                    <p class="m-0 font-weight-bold text-muted text-center">It seems like you are attempting to pick the wrong Item.</p>
                </div>

            </div>
        </div>
    </div>
</div>
<div id="undo-modal" class="modal fade" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body p-5 text-center">
                
                <div class="icon-lg-pop mb-4 text-center">
                    <i class="material-icons ">undo</i>
                </div>
                <p class="m-0 font-weight-bold text-center">Undo Pick</p>
                <p class="m-0 font-weight-bold mb-2 text-muted text-center">Proceed to undo this pick?</p>

                <input value="3" id="rak_return_id" type="hidden" name="">
                <button id="btn_undoSave" class="btn btn-sm btn-success px-4">Yes</button>
                <button data-dismiss="modal" class="btn btn-sm btn-danger px-4">No</button>
            </div>
        </div>
    </div>
</div>

<?php

    /* preload view if url param exist */
    $__i = Sanitizer::filter('i', 'get');
    $__n = Sanitizer::filter('n', 'get');
    $__m = Sanitizer::filter('m', 'get');
    $__c = Sanitizer::filter('c', 'get');
    $__t = Sanitizer::filter('t', 'get');

    if($__i && $__n && $__m) {
        ?>
        <script>
            $(function(){
                pickOrder('<?= $__i ?>', '<?= $__n ?>', '<?= $__m ?>', '<?= $__c ?>')
            })
        </script>
        <?php
    }
    
    /* toast a message if url param exist */

    if($__t) {
        ?>
        <script>
            $.Toast("<?= $__t ?>", {
                'duration': 4000,
                'position': 'top',
                'align': 'right',
                'class': 'bg-info'
            });
            window.history.replaceState(null, null, window.location.pathname);
        </script>
        <?php
    }

?>
<?php
require_once "./component/footer.php";

