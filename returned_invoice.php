<?php

require_once "./component/import.php";
$meta_title = 'Returned Invoice - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";
require_once "model/model.returned.php";
$returned = new Returned();
$slip_no = (isset($_GET["slip_no"]) && !empty($_GET["slip_no"])) ? $_GET["slip_no"] : false; ?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/lib/jquery/scanner.js"></script>
<script src="/wms/services/returned/returned.js"></script>

<div class="main-content full-page">

    <div class="row row-cols-1">
        <div class="col">
            <div class="padded mb-5">
                <h1 class="mt-5"><i class="material-icons mr-3">receipt</i> Completed Invoice</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">Completed Invoice</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <?php if(!$slip_no) { ?>
    <div class="padded">
        <div class="image-area" id="scannable">
            <div class="content text-center">
                <i class="material-icons">qr_code</i>
                <p class="text-muted mt-1">
                Please scan the slip number or enter the slip number<br>
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
        $order = $returned->getOrder($slip_number);

        if(!empty($order)) {
            foreach($order as $k=>$v) {

                $slip_id = $v['slip_id'];
                $order_details = $returned->getAllOrdersdetails($slip_id);            
                $itemstocheck = $returned->countItemstoCheck($slip_id);
                $count_undo = $returned->countUndo($slip_id);
                $order[$k]["items_to_check"] = $itemstocheck;

                foreach ($order_details as $key => $x) { 
    
                    $product_id = $x['product_id'];
                    $product_lots = $returned->getAllLots($slip_id,$product_id,"cart");
                    $order_details[$key]["lot"] = $product_lots;
                }

                $order[$k]["order_details"] = $order_details;
            }
        ?>
    <div class="padded">  
        <div class="row">
            <?php 
                foreach($order as $key => $value) {
                    $order_details = $order[$key]["order_details"];
            ?>
            
            <div class="col col-sm-12 col-md-4 mb-4">
                <div>
                    <div class="border">
                        <div class="w-100 bg-white">
                            <div class="row row-cols-1 m-0">
                                <div class="p-5 mb-2 bg-light col">
                                    <label class="m-0 text-muted"><small>PO#: <?= $value["po_no"] ?></small></label>
                                    <h5 class="m-0"><b>#<span class="slip_no_value"><?= $value["slip_no"] ?></span></b></h5>
                                    <p class="m-0 mt-2 text-muted"><small><?= $value["bill_to"] ?></small></p>
                                    <p class="m-0 mt-n2 text-muted"><small><b><?= $value["slip_order_date"] ?>, <?= $value["ship_to"] ?></b></small></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pb-5 px-5 pt-3">
                            <p class="mb-3"><b>REF:</b> <?= $value["reference"] ?></p>
                            <input type="hidden" value="<?php echo $count_undo ?>" id="undoItem" name="">
                            <button type="button" onclick="finished_transaction(<?php echo $slip_id; ?>)" class="btn btn-sm btn-primary mb-2">Finish Transaction</button>
                            <button type="button" onclick="reship_Order(<?php echo $slip_id; ?>)" class="btn btn-sm btn-secondary mb-2">Re-ship</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col col-sm-12 col-md-8">
                <div class="row mb-5">
                <?php 
                    foreach($order_details as $k => $v) {
                        $new_weight = $v['product_weight'] * $v['stock_qty'];
                        $product_image = "product_image/".$v['product_code'].".jpg"; ?>
                        <div class="col col-sm-12">
                            <div class="check-card has-image border">
                                
                                <div class="check-image mb-2 border-right">
                                    <img src="<?= "product_image/".$v['product_code'].".jpg"?>" onError="this.onerror=null;this.src='product_image/dummy.jpg';" class="img-fluid" />
                                </div>
                                
                                <p class="check-title mb-0 text-muted"><small>Expires @ <?= date('F Y',strtotime($v['stock_expiration_date'])) ?></small></p>
                                <p class="check-title mb-0"><?= $v["product_description"] ?></p>
                                <p class="check-title mb-0 text-muted"><small>Code: <b><?= $v["product_code"] ?></small></b></p>

                                <div class="check-ordered d-inline">
                                    
                                    <p class="check-title mb-0 mt-3 text-muted"><small>Lot/SN: <b><?= $v["stock_lotno"] . " " . $v["stock_serialno"] ?></small></b></p>
                                    <p class="check-title mb-0 text-muted"><small>UoM: <b><?= $v["unit_name"] ?></small></b></p>
                                    <p class="check-title mb-0 text-muted"><small>Picked / Ordered</small></b></p>
                                    <p class="check-title mb-0 text-muted"><small><b><?= $v["stock_qty"] ?></b> / <b><?= $v["stock_qty"] ?></b></small></b></p>
                                
                                </div>
                                <button type="button" class="btn btn-sm mt-3 btn-warning text-white" onclick="returnItem(<?php echo $v['id']; ?>,<?php echo $v['stock_qty']; ?>)">Return this item</button>
                            </div>



                        </div>


                <?php } ?>    
                </div>

                <center>
                    <button class="btn btn-md btn-danger text-white" onclick="returnAll(<?php echo $slip_id;?>)">RETURN ALL</button>
                </center>
            </div>

            <?php } ?>
            
            <div class="col col-sm-12">    
                        
                <div>
                    <div class="input-group padded">
                        <h2 class="mb-4 center-align">Item(s) to return</h2>
                    </div>
                    <div class="input-group mb-4 padded">
                        <div class="input-group-prepend">
                            <button class="btn btn-primary rounded-0 add-field" disabled="" type="button" data-toggle="modal" data-target="#unitModal"><i class="material-icons myicon-lg">search</i></button>
                        </div>
                        <input id="dataTableSearch" type="search" class="form-control rounded-0 search-field" placeholder="Search here">
                    </div>
                    <div class="responsive-table">
                        <table class="table bg-white table-bordered" id="return_table">
                            <thead>
                                <th>Product</th>
                                <th>Lot no</th>
                                <th>Expiration Date</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                <!-- ajaxial content -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } else { ?>

        <div class="padded">
            <div class="col p-0">
                <div class="card-panel p-4 border-0 empty rounded-lg">
                    <p class="m-0 text-muted"><small><?= date("jS \of M Y") ?></small></p>
                    <p class="m-0 font-weight-bold">There are no matching orders that matched the scanned document.<br>Please try scanning <a href="returned_invoice.php"><u>again</u></a></p>
                </div>
            </div>
        </div>
        
        <?php
        }
    } 
    
    ?>


</div>

        

<div class="modal fade ios" id="return_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Return Item</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="returnStockid" name="">
        <label>Input Quantity to return</label>
        <input type="number" id="returnQty" class="form-control" name="">
        <!-- <label>Quarantine Area </label> -->
        <input type="hidden" id="quarantineArea" value="" class="form-control" name="">
      </div>
      <div class="modal-footer">
        <button type="button" onclick="quarantineItem()" class="btn btn-primary">Quarantine</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php
require_once "./component/footer.php";
