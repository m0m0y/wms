<?php

require_once "./component/import.php";
$meta_title = 'Checking - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";
require_once "model/model.checking.php";
$checking = new Checking();
$slip_no = (isset($_GET["slip_no"]) && !empty($_GET["slip_no"])) ? $_GET["slip_no"] : false; ?>

<script src="/wms/lib/jquery/scanner.js"></script>
<script src="/wms/services/checking/checking.js?v=5676567657567657"></script>

<div class="main-content full-page">
    <?php if(!$slip_no) { ?>
        

    <div class="row row-cols-1">
        <div class="col">
            <div class="padded mb-5">
                <h1 class="mt-5"><i class="material-icons mr-3">done_all</i> Checking</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">Checking</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="padded">
        <div class="image-area" id="scannable">
            <div class="content text-center">
                <i class="material-icons">qr_code</i>
                <p class="text-muted mt-1">
                    Please scan the slip number to be checked or enter the slip number<br>
                    <button class="btn btn-sm btn-primary mt-3" data-toggle="modal" data-target="#manualCheck">manually</button>
                </p>
            </div>
        </div>
    </div>

    <div class="modal fade ios" id="manualCheck" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enter Slip No</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="text" id="manual-check" class="form-control rounded-0" placeholder="Type Slip No">
                </div>
                <div class="modal-footer">
                    <button type="button" id="manual-submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <?php } 
    
    if($slip_no) { 
        
        $slip_number = $slip_no;
        $order = $checking->getOrder($slip_number);

        if(!empty($order)) {
            foreach($order as $k=>$v) {

                $slip_id = $v['slip_id'];
                $order_details = $checking->getAllOrdersdetails($slip_id);            
                $itemstocheck = $checking->countItemstoCheck($slip_id);
                $order[$k]["items_to_check"] = $itemstocheck;

                foreach ($order_details as $key => $x) { 
    
                    $product_id = $x['product_id'];
                    $product_lots = $checking->getAllLots($slip_id,$product_id,"cart");
                    $order_details[$key]["lot"] = $product_lots;
                }

                $order[$k]["order_details"] = $order_details;
            }
        ?>
            
        <div class="row">
            <?php 
                foreach($order as $key => $value) {
                    $order_details = $order[$key]["order_details"];
            ?>
            <div class="col col-sm-12">
                <div class="padded mb-5">
                    <h1 class="mt-5"><?= $value["bill_to"] ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent pl-0 mb-0">
                            <li class="breadcrumb-item active"><b>PO:</b>&nbsp;<?= $value["po_no"] ?></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= date('M j Y', strtotime($value["slip_order_date"])) ?></li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="col col-sm-12 col-md-4 mb-4">
                <div class="padded">
                    <div class="border">
                        <div class="w-100 bg-white">
                            <div class="row row-cols-1 m-0">
                                <div class="p-5 mb-2 bg-light col">
                                    <label class="m-0 text-muted"><small>PO#: <?= $value["po_no"] ?></small></label>
                                    <h5 class="m-0"><b>#<?= $value["slip_no"] ?></b></h5>
                                    <p class="m-0 mt-2 text-muted"><small><?= $value["bill_to"] ?></small></p>
                                    <p class="m-0 mt-n2 text-muted"><small><b><?= $value["slip_order_date"] ?>, <?= $value["ship_to"] ?></b></small></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pb-5 px-5 pt-3">
                            <p class="mb-1"><b>REF:</b> <?= $value["reference"] ?></p>
                            <p>Item to check: <b id="itemstocheck"><?= $value["items_to_check"] ?></b></p>
                            <button type="button" onclick="pack_order(<?php echo $slip_id; ?>)" class="btn btn-sm btn-primary mb-2">For Packing</button>
                            <button type="button" onclick="reinvoice_order(<?php echo $slip_id; ?>)" class="btn btn-sm btn-secondary mb-2">Reinvoice Order</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col col-sm-12 col-md-8">
                <div class="row mb-5 padded">
                <?php 
                    foreach($order_details as $k => $v) {
                        $new_weight = $v['product_weight'] * $v['quantity_order'];
                        $weightper_unit = $v['product_weight']; 
                        $product_image = "product_image/".$v['product_code'].".jpg"; ?>
                        <div class="col col-sm-12">
                            <div class="check-card has-image border">
                                
                                <div class="check-image mb-2 border-right">
                                    <img src="<?= "product_image/".$v['product_code'].".jpg"  ?>" onError="this.onerror=null;this.src='product_image/dummy.jpg';" class="img-fluid" />
                                </div>
                                
                                <!-- <p class="check-title mb-0 text-muted"><small>Expires @ <?= date('F Y',strtotime($v['lot'][0]['stock_expiration_date'])) ?></small></p> -->
                                <p class="check-title mb-0 text-muted"><small><?= $v["quantity_order"] ?> <?= $v['unit_name'] ?></small></p>
                                <p class="check-title mb-0"><?= $v["product_description"] ?></p>
                                
                                <p class="check-title mb-0 text-muted"><small>Code: <b><?= $v["product_code"] ?></small></b></p>

                                <?php if ($v['checking_status']!="approved"){ ?>
                                    <button type="button" class="btn btn-primary btn-sm mt-2 btn_approve"  
                                    data-qty_order="<?= $v['quantity_order'] ?>"
                                    data-weight="<?= $new_weight ?>"
                                    data-weightper_unit="<?= $weightper_unit ?>"
                                    data-id="<?= $v['id'] ?>"
                                    data-uom="<?= $v['unit_name'] ?>"
                                    ><i class="material-icons myicon-lg mr-2">error_outline</i> Validate</button>
                                <?php } else { ?>
                                    <button type="button" class="btn btn-danger btn-sm mt-2"  
                                    onclick="undoApprove(<?php echo $v['id']; ?>)"
                                    ><i class="material-icons myicon-lg mr-2">history</i> Undo</button>
                                <?php } ?>
                                
                            </div>

                        </div>
                <?php } ?>    
                </div>
            </div>

            <?php } ?>
        </div>
    <?php } else {
        ?>
        <div class="col p-0">
            <div class="card-panel p-4 border-0 empty rounded-lg">
                <p class="m-0 text-muted"><small><?= date("jS \of M Y") ?></small></p>
                <p class="m-0 font-weight-bold">There are no matching orders that matched the scanned document.<br>Please try scanning <a href="checking.php"><u>again</u></a></p>
            </div>
        </div>

        <?php
        }

    } ?>
</div>


<div class="modal fade ios" id="check_modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-0">
            
            <div class="modal-header">
                <h5 class="modal-title">Check Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <label>Quantity Picked</label>
                
                <div class="input-group mb-3">
                    <input type="number" id="quantity_picked" min="0" class="form-control rounded-0" placeholder="Picked items">
                    <div class="input-group-append">
                        <span class="input-group-text rounded-0" id="pick-to-compare">NaN kg</span>
                    </div>
                </div>

                
                <div class="row row-cols-1 steps" id="unavailable" style="display: none;">
                    <div class="col">
                        <div class="alert alert-warning" role="alert">
                            Weight validation unavailable.<br>selected item's weight is empty.
                        </div>
                    </div>
                </div>

                <div class="row row-cols-1 steps" id="v-step-1">
                    <div class="col">
                        <label>Weight Validation</label><br>
                        <button type="button" class="btn btn-primary validate-weight" data-type='bulk'>Bulk Validation</button>
                        <button type="button" class="btn btn-secondary validate-weight" data-type="single">One by one</button>
                    </div>
                </div>

                <div class="row row-cols-1 steps" id="v-step-2">

                    <div class="single col">
                        <label>Weight of Picked item:</label>

                        <label class="ml-2"><small class="text-muted font-weight-bold"><span id="one-by-pick">0</span> / <span id="one-by-total">9</span></small></label>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text p-0">
                                    <button class="btn clearer" data-to="#weight_picked_perUnit" type="button">
                                        <i class="material-icons myicon-lg">sync</i>
                                    </button>
                                </span>
                            </div>
                            <input type="number" id="weight_picked_perUnit" step="0.0001" min="0" class="form-control rounded-0" placeholder="Weight to compare">
                            <div class="input-group-append">
                                <span class="input-group-text p-0" data-to="#weight_picked_perUnit">
                                    <button class="btn paster" data-to="#weight_picked_perUnit" type="button">
                                        <i class="material-icons myicon-lg">content_paste</i>
                                    </button>
                                </span>
                                <span class="input-group-text rounded-0" id="weightper_unit_to_compare">{} kg</span>
                            </div>
                        </div>
                    </div>

                    <div class="bulk col">
                        <label>Total Weight</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend total-clearer">
                                <span class="input-group-text p-0">
                                    <button class="btn clearer" data-to="#weight_picked" type="button">
                                        <i class="material-icons myicon-lg">sync</i>
                                    </button>
                                </span>
                            </div>
                            <input type="number" id="weight_picked" step="0.0001" min="0" class="form-control rounded-0" placeholder="Gross Weight">
                            <div class="input-group-append">

                                <span class="input-group-text p-0 total-paster">
                                    <button class="btn paster" data-to="#weight_picked" type="button">
                                        <i class="material-icons myicon-lg">content_paste</i>
                                    </button>
                                </span>

                                <span class="input-group-text rounded-0" id="weight-to-compare">{} kg</span>
                            </div>
                        </div>
                    </div>

                </div>


                <p class="bg-warning" id="weight_requirement"></p>
                <input type="hidden" value="0" id="count_weightedItem" name="">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary reset-validation"><i class="material-icons myicon-lg mr-2">rotate_left</i> Reset</button>
                <button type="submit" style="display:none;" class="btn btn-primary btn_validate"><i class="material-icons myicon-lg mr-2">save_alt</i> Mark as checked</button>
            </div>
            
        </div>
    </div>
</div>

<?php
require_once "./component/footer.php";
