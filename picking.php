<?php

require_once "./component/import.php";
$meta_title = 'Picking - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";
require_once "model/model.picking.php";

$picking = new Picking();
$user_id = $auth->getSession("logid");
$role = $auth->getSession("role");
?>
<style>
.control-group input { pointer-events: all; }
.control-group-submit, .control-group { max-width: 160px; }
</style>

<script src="/wms/lib/jquery/scanner.js"></script>
<script src="/wms/services/picking/picking.js?v=beta-11"></script>

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
                
                <h1 class="mt-5"><i class="material-icons mr-3">local_grocery_store</i> Picking</h1>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">Picking</li>
                    </ol>
                </nav>

            </div>
        </div>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 padded">
        <?php
        
        $order = $picking->getAllOrders($user_id,$role);
        /* echo '<pre>';
        print_r($order);
        echo '</pre>'; */
        if(!empty($order)){
        foreach($order as $k=>$v) {
            $slip_id = $v['slip_id'];
            $slip_no = $v['slip_no'];
            $idforUser = "user_id".$slip_id;
            $customer_name = ucfirst($v['bill_to']);
            $ship_address = ucfirst($v['ship_to']);
            $pick_percentage = ($v['total_picked']) ? number_format(($v['total_picked']/$v['total_qty']) * 100, "1", ".", ",") : 0;
            $repick = ($v['order_status']=="repick") ? 'border-dashed border border-warning' : '';
            $admin = ($role == "admin") ? "display:block" : "display:none";
        ?>
        <div class="col">
            <div class="card-panel p-4 <?= $repick ?>" onclick="pickOrder('<?= $slip_id ?>', '<?= $ship_address ?>', '<?= $slip_no ?>')">
                <p class="m-0 text-muted"><small><?= date("jS \of M Y", strtotime($v['slip_order_date'])) ?></small></p>
                <p class="m-0 font-weight-bold"><?= $slip_no ?></p>
                
                <div class="m-0 mb-3 d-flex justify-content-center row">
                    <div class="col-md-6 col-sm-12 p-0">
                        <p class="m-0"><?= $customer_name ?></p>
                        <p class="m-0"><small><?= $ship_address ?></small></p>
                    </div>
            
                    <div class="col-md-6 col-sm-12 p-0">
                        <?php if($role == "admin" || $role == "admin-default"): ?>
                            <div class="controls">
                                <input type="hidden" id="<?php echo $idforUser ?>" value="<?=$v['user_id']?>">
                                <a href="#!" onclick="event.stopPropagation();updateUser('<?= $slip_id ?>')" tabindex="-1">
                                    <i class="material-icons">person_search</i>
                                </a>
                                <?php if ($v['total_picked']==0): ?>
                                <a href="#!" onclick="event.stopPropagation();deleteOrder('<?= $slip_id ?>')" tabindex="-1">
                                    <i class="material-icons">delete</i>
                                </a>
                                <?php endif ?>
                            </div>
                        <?php endif ?>
                    </div>
                </div>

                <?php if ($v['order_status']=="repick" && !empty($v['comments'])): ?>
                   
                    <p class="px-3 py-2 comment rounded-0">
                        <small><b>Return Comments:</b> <?php echo $v['comments']; ?></small>
                    </p>
                <?php endif ?>

                <?php if ($v['order_status']=="prepare" && !empty($v['remarks'])): ?>
                    <p class="px-3 py-2 comment rounded-0">
                        <small><b>Remarks:</b> <?php echo $v['remarks']; ?></small>
                    </p>
                <?php endif ?>

                <!-- <p class="right-align mb-1"><small><b><?= $v['total_picked'] ?> / <?= $v['total_qty'] ?></b></small></p> -->
                <div class="progress mb-3 rounded-0" style="height: 10px;">
                    <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $pick_percentage ?>%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            
        </div>
        <?php 
        } } else {
        ?>

        <div class="col">

            <div class="card-panel p-4 border-0 empty rounded-lg">
                <p class="m-0 text-muted"><small><?= date("jS \of M Y") ?></small></p>
                <p class="m-0 font-weight-bold">There are no orders to pick</p>
            </div>

        </div>

        <?php
        }
        ?>
    </div>
</div>


<div class="modal fade ios" id="users_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Change Assigned Picker</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <select required class="form-control rounded-0" name="users" id="users" title="Unit of Measurement">
        </select>
        <input type="hidden" id="slip_idforpicker">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" onclick="savenewUser()" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>


<input type="hidden" id="viewcache_id" />
<input type="hidden" id="viewcache_name" />
<input type="hidden" id="viewcache_no" />

<div id="manual-modal" class="modal fade" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                
                <div class="icon-lg-pop my-4 text-center">
                    <i class="material-icons ">how_to_reg</i>
                </div>
                <p class="mb-5" id="toTable" data-target="0"><b>Continue this process?</b><br>Please input the lot number to proceed.</p>
                <input type="text" id="lotnumber_manual" class="form-control" placeholder="lot number..." name="">
                <button class="btn btn-success mt-2 btn submit_btn" onclick="submitManual()">Submit</button>
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
                    <p id="pickingForm" class="d-none">
                        <input type="hidden" id="order_details_id" />
                        <input type="hidden" id="stock_id" />
                        <input type="hidden" id="product_id"  />
                        <input type="hidden" id="stock_lotno" />
                        <input type="hidden" id="stock_serialno" />
                        <input type="hidden" id="stock_qty" />
                        <input type="hidden" id="stock_expiration_date" />
                        <input type="hidden" id="location_id" />
                    </p>
                    
                    <p class="mb-2"><b>Lot Number Verified</b><br>Please enter the quantity to be picked</p>
                    
                    <div class="input-group mb-3 control-group">
                        <div class="input-group-append">
                            <button class="pick-control input-group-text" onmousedown="minusDown()" onmouseup="minusUp()" data-type="-" data-target="#pickingQuantity">-</button>
                        </div>
                        <input type="number" id="pickingQuantity" class="form-control">
                        <div class="input-group-prepend">
                            <button class="pick-control input-group-text" onmousedown="addDown()" onmouseup="addUp()" data-type="+" data-target="#pickingQuantity">+</button>
                        </div>
                    </div>
                    <button class="btn btn-success mb-3 control-group-submit" onclick="chooseCart()">Pick Item</button>
                    
                    <!-- <div class="input-group qty-group mb-3">
                        <input type="number" id="pickingQuantity" class="form-control">
                        <div class="input-group-append">
                            <button class="input-group-text" onclick="chooseCart()">Pick Item</button>
                        </div>
                    </div> -->
                </div>

                <div class="mb-4 text-center" >
                    <div id="selection" style="display: none;">
                        <input type="hidden" id="undo_pick" class="form-control">

                        <div class="icon-lg-pop my-4 text-center">
                            <i class="material-icons ">beenhere</i>
                        </div>
                        <p class="mb-2 text-center"><b>You're been there</b><br>Please choose one to proceed</b></p><br>
                        <button class="btn btn-primary mb-3 control-group-submit" onclick="scan_pick()">Scan Barcode</button>
                        <button class="btn btn-success mb-3 control-group-submit" onclick="manual_pick()">Manual Input</button>
                    </div>
                </div>

                <div id="choose-cart" class="text-left" style="display:none;">
                    <div class="icon-lg-pop my-4 text-center">
                        <i class="material-icons">qr_code</i>
                    </div>
                    <p class="mb-5 text-center mb-2"><b>Almost there!</b><br>Please scan your <b class="text-primary">cart</b> now</p>
                    <input type="hidden" class="form-control d-inline" id="pickingCart">
                </div>
                
            </div>
        </div>
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

<div id="table-modal" class="modal fade" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">

                <div class="mb-4" >
                    <div id="choices">
                        <div class="icon-lg-pop my-4 text-center">
                            <i class="material-icons ">beenhere</i>
                        </div>
                        <p class="mb-2 text-center"><b>You're been there</b><br>Please choose one to proceed</b></p><br>
                        <button class="btn btn-primary mb-3 control-group-submit" onclick="scanTable()">Scan Barcode</button>
                        <button class="btn btn-success mb-3 control-group-submit" onclick="manualInputTable()">Manual Input</button>
                    </div>
                </div>
                
                <div id="scan-table" style="display: none;">
                    <div class="icon-lg-pop my-4 text-center">
                        <i class="material-icons ">qr_code</i>
                    </div>
                    <p class="mb-5" id="toTable" data-target="0"><b>Finalize this order's picking?</b><br>Please scan the invoice table inorder to proceed.</p>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="undo-modal" class="modal fade" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">

                <div id="undoA">
                    <div class="icon-lg-pop my-4 text-center">
                        <i class="material-icons ">undo</i>
                    </div>
                    <p class="mb-2 text-center"><b>Undoing Pick</b><br>Enter the quantity to be remove</p>
                    
                    <div class="input-group mb-3 control-group">
                        <div class="input-group-append">
                            <button class="pick-control input-group-text" data-type="-" data-target="#undo_pickingQuantity">-</button>
                        </div>
                        <input type="text" id="undo_pickingQuantity" class="form-control">
                        <div class="input-group-prepend">
                            <button class="pick-control input-group-text" data-type="+" data-target="#undo_pickingQuantity">+</button>
                        </div>
                    </div>
                    <button class="btn btn-danger mb-3 control-group-submit d-block" onclick="undoB()">Unpick Item</button>

                </div>

                <div class="mb-4 text-center" >
                    <div id="undoC" style="display: none;">
                        <input type="hidden" id="undo_pick" class="form-control">

                        <div class="icon-lg-pop my-4 text-center">
                            <i class="material-icons ">beenhere</i>
                        </div>
                        <p class="mb-2 text-center"><b>You're been there</b><br>Please choose one to proceed</b></p><br>
                        <button class="btn btn-primary mb-3 control-group-submit" onclick="scan()">Scan Barcode</button>
                        <button class="btn btn-success mb-3 control-group-submit" onclick="manual()">Manual Input</button>
                    </div>
                </div>

                <div class="mb-4 text-center" >
                    <div id="undoB" style="display: none;">
                        <div class="icon-lg-pop my-4 text-center">
                            <i class="material-icons ">qr_code</i>
                        </div>
                        <p class="mb-2 text-center"><b>One last thing</b><br>Please scan rak to proceed</b></p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div id="rak-manual-modal" class="modal fade" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <input type="hidden" id="undo_pickQuan" class="form-control">

                <div class="icon-lg-pop my-4 text-center">
                    <i class="material-icons ">how_to_reg</i>
                </div>
                <p class="mb-5" id="toTable" data-target="0"><b>One last thing</b><br>Please select rak to proceed.</p>
                <select required id="rak_id" name="rak_id" class="form-control rounded-0 mb-3"></select>
                <button class="btn btn-success mt-2 btn confirm-btn" onclick="undoSaveManual()">Submit</button>
            </div>
        </div>
    </div>
</div>

<div id="cart-manual-modal" class="modal fade" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="icon-lg-pop my-4 text-center">
                    <i class="material-icons ">how_to_reg</i>
                </div>
                <p class="mb-5" id="toTable" data-target="0"><b>Almost there!</b><br>Please select  your <b class="text-primary">cart</b> to proceed.</p>
                <select required id="cart_id" name="cart_id" class="form-control rounded-0 mb-3"></select>
                <button class="btn btn-success mt-2 btn confirm-btn" onclick="cartPick()">Submit</button>
            </div>
        </div>
    </div>
</div>

<div id="table-manual-modal" class="modal fade" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="icon-lg-pop my-4 text-center">
                    <i class="material-icons ">how_to_reg</i>
                </div>
                <p class="mb-5" id="toTable" data-target="0"><b>Finalize this order's picking?</b><br>Please select the invoice table inorder to proceed.</p>
                <select required id="table_id" name="table_id" class="form-control rounded-0 mb-3"></select>
                <button class="btn btn-success mt-2 btn confirm-btn" onclick="tablePick()">Submit</button>
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
                'align': 'left',
            });
            window.history.replaceState(null, null, window.location.pathname);
        </script>
        <?php
    }

?>
<?php
require_once "./component/footer.php";

