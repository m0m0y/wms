<?php

require_once "./component/import.php";
$meta_title = 'Shipping - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";
require_once "model/model.shipping.php";

$shipping = new Shipping();

?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/lib/jquery/scanner.js"></script>

<script src="/wms/services/shipping/shipping.js?v=beta-11"></script>

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
                
                <h1 class="mt-5"><i class="material-icons mr-3">local_shipping</i> Shipping</h1>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">Shipping</li>
                    </ol>
                </nav>

            </div>
        </div>
    </div>
    <div class="row row-cols-1 row-cols-sm-2">
        <?php
        
        $order = $shipping->getAllOrders();
        /* echo '<pre>';
        print_r($order);
        echo '</pre>'; */
        if(!empty($order)){
        foreach($order as $k=>$v) {
            $slip_id = $v['slip_id'];
            $slip_no = $v['slip_no'];
            $customer_name = ucfirst($v['ship_to']);
            
            $repick = ($v['order_status']=="repick") ? 'border border-warning' : '';
            $count_box = $shipping->countBoxPerOrder($slip_id);
            $count_pickedbox = $shipping->countpickedBox($slip_id);
            $pick_percentage = ($count_pickedbox) ? number_format(($count_pickedbox/$count_box) * 100, "1", ".", ",") : 0;
        ?>

        <div class="col">
            <div class="padded">
                <div class="card-panel p-4 <?= $repick ?>" onclick="pickOrder('<?= $slip_id ?>', '<?= $customer_name ?>', '<?= $slip_no ?>')">
                    <p class="m-0 text-muted"><small><?= date("jS \of M Y", strtotime($v['slip_order_date'])) ?></small></p>
                    <p class="m-0 font-weight-bold">Slip No: <?= $slip_no ?></p>
                    <p class="m-0 mb-3"><?= $customer_name ?></p>

                    <?php if ($v['order_status']=="repick"): ?>
                        <p class="comment"><?php echo $v['comments']; ?></p>
                    <?php endif ?>

                    <!-- <p class="right-align mb-1"><small><b><?= $count_pickedbox ?> / <?= $count_box ?></b></small></p> -->
                    <div class="progress mb-3 rounded-0" style="height: 10px;">
                        <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $pick_percentage ?>%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <div class="controls">
                        <a href="#!" onclick="event.stopPropagation();repack('<?= $slip_id ?>')"  tabindex="-1">
                            <i class="material-icons">refresh</i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <?php 
        } } else {
        ?>

        <div class="col">
            <div class="padded">
                <div class="card-panel p-4 border-0 empty rounded-lg">
                    <p class="m-0 text-muted"><small><?= date("jS \of M Y") ?></small></p>
                    <p class="m-0 font-weight-bold">There are no orders ship</p>
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

<div id="validity-modal" class="modal fade" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="validity-success" class="modal-body text-center">
                <div id="choose-qty" style="display: block;">
                    
                    <div class="card-panel p-4 border-0 mb-0 rounded-0 text-cente" data-dismiss="modal">
                        <div class="icon-lg-pop mb-4 text-center">
                            <i class="material-icons text-success">done_all</i>
                        </div>
                        <p class="m-0 font-weight-bold text-center">Box Number Verified</p>
                        <p class="m-0 font-weight-bold text-muted text-center">Please confirm that you are<br>about to put the item in the truck.</p>
                    </div>
                    
                    <p id="pickingForm" class="d-none">
                        <input type="hidden" id="box_number" name="" />
                    </p>

                    <div class="input-group qty-group mb-5">
                        <input type="hidden" id="pickingQuantity" class="form-control">
                        <button class="btn btn-sm d-block m-auto btn-success px-4" onclick="chooseCart()">Confirm</button>
                    </div>

                </div>

                <div id="choices" style="display: none;">
                    <input type="hidden" id="undo_pick" class="form-control">

                    <div class="icon-lg-pop my-4 text-center">
                        <i class="material-icons">beenhere</i>
                    </div>
                    <p class="mb-2 text-center"><b>You're been there</b><br>Please choose one to proceed</b></p><br>
                    <button class="btn btn-primary mb-3 control-group-submit" onclick="scanTruck()">Scan Barcode</button>
                    <button class="btn btn-success mb-3 control-group-submit" onclick="manualInputTruck()">Manual Input</button>
                </div>

                <div id="choose-cart" style="display: none;">
                    <div class="icon-lg-pop mb-4 text-center">
                        <i class="material-icons text-success">local_shipping</i>
                    </div>
                    <p class="m-0 font-weight-bold text-center">You're Almost There</p>
                    <p class="m-0 font-weight-bold text-muted text-center mb-4">Please scan the truck now.</p>
                    <input type="hidden" class="form-control d-inline" id="pickingCart">
                </div>
                
            </div>
        </div>
    </div>
</div>

<div id="validity-fail" class="modal fade" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="card-panel p-4 border-0 mb-0 rounded-0 text-cente" data-dismiss="modal">
                    <div class="icon-lg-pop mb-4 text-center">
                        <i class="material-icons text-danger">location_off</i>
                    </div>
                    <p class="m-0 font-weight-bold text-center">Wrong box number</p>
                    <p class="m-0 font-weight-bold text-muted text-center">It seems like you are attempting to ship the wrong Item.</p>
                </div>
            </div>
        </div>
        
    </div>
</div>

<div id="undo-modal" class="modal fade" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="icon-lg text-white bg-warning text-center shadow" data-dismiss="modal">
                    <i class="material-icons">report_problem</i>
                </div>
                <p class="mb-5">You are about to <b>undo</b> this item(s)' picking.<br>Please scan the next location inorder to proceed.</p>
            </div>
        </div>
    </div>
</div>



<div class="modal fade ios" id="delivery_modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-0">
            
            <div class="modal-header">
                <h5 class="modal-title">Deliver Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <input type="hidden" id="deliver_slip_id" min="0" class="form-control rounded-0" placeholder="Input DO #">
                <label><b>DO #</b></label>
                
                <div class="input-group mb-3">
                    <input type="text" id="do_number" min="0" class="form-control rounded-0" placeholder="Input DO #">
                    
                </div>

                <label><b>Assign To</b></label>
                
                <div class="input-group mb-3">
                    <select class="form-control" name="assign_to" id="assign_to"></select>
                </div>




                <p class="bg-warning" id="weight_requirement"></p>
                <input type="hidden" value="0" id="count_weightedItem" name="">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btn_deliver"><i class="material-icons myicon-lg mr-2">rotate_left</i> Submit</button>
            </div>
            
        </div>
    </div>
</div>


<div id="manual-modal" class="modal fade" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                
                <div class="icon-lg-pop my-4 text-center">
                    <i class="material-icons ">how_to_reg</i>
                </div>
                <p class="mb-5" id="toTable" data-target="0"><b>Continue this process?</b><br>Please input the box number to proceed.</p>
                <div class="input-group mb-3">
                    <input type="text" id="shipping_barcode" class="form-control" placeholder="Box Number..." name="" disabled>
                    <div class="input-group-append">

                        <span class="input-group-text p-0 total-paster">
                            <button class="btn paster" onclick="copy_box_number()" type="button">
                                <i class="material-icons myicon-lg">content_paste</i>
                            </button>
                        </span>

                        <span class="input-group-text rounded-0" id="box_number_compare"></span>
                    </div>
                </div>
                <button class="btn btn-success mt-2 btn submit_btn" onclick="submitManual()">Submit</button>
            </div>
        </div>
    </div>
</div>


<div id="truck-manual-modal" class="modal fade" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                
                <div class="icon-lg-pop my-4 text-center">
                    <i class="material-icons ">how_to_reg</i>
                </div>
                <p class="mb-5" id="toTable" data-target="0"><b>You're Almost There</b><br>Please select the truck now.</p>
                <select required id="truck_id" name="truck_id" class="form-control rounded-0 mb-3"></select>
                <button class="btn btn-success mt-2 btn submit_btn" onclick="submitTruckManual()">Submit</button>
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