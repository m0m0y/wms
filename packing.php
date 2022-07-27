<?php

require_once "./component/import.php";
$meta_title = 'Packing - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";
require_once "model/model.packing.php";
$packing = new Packing();

/* placeholder to totalboxes */
$total_boxes = array();

$slip_no = (isset($_GET["slip_no"]) && !empty($_GET["slip_no"])) ? $_GET["slip_no"] : false; ?>

<script src="/wms/lib/jquery/scanner.js"></script>
<script src="/wms/services/packing/packing.js"></script>

<div class="main-content full-page">
    <?php if(!$slip_no) { ?>
        

    <div class="row row-cols-1">
        <div class="col">
            <div class="padded mb-5">
                <h1 class="mt-5"><i class="material-icons mr-3">business_center</i> Packing</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">Packing</li>
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
        $order = $packing->getOrder($slip_number);

        if(!empty($order)) {
            foreach($order as $k=>$v) {

                $slip_id = $v['slip_id'];
                $count_unbox = 0; $count_box = 0;
                $order_details = $packing->getAllOrdersdetails($slip_id);
                $total_boxes = $total_raw_boxes = array();


                foreach ($order_details as $key => $x) { 
    
                    $product_id = $x['product_id'];

                }

                $order[$k]["order_details"] = $order_details;
            }
        ?>
            
        <div class="row">
            <?php 
                foreach($order as $key => $value) {
                    $order_details = $order[$key]["order_details"];
            ?>

            <div class="col">
                <div class="pt-5 pb-5">
                  <input type="hidden" id="id_slip" value="<?= $slip_id ?>">
                  <input type="hidden" id="no_slip" value="<?= $slip_no ?>">
                  <div class="r-container">
                  <div class="receipt bg-white border border-dashed">
                    <div class="row row-cols-2 m-0">
                      <div class="p-5 mb-2 bg-light col">
                        <label class="m-0 text-muted"><small>PO#: <?= $v["po_no"] ?></small></label>
                        <h5 class="m-0"><b>#<?= $v["slip_no"] ?></b></h5>

                        <p class="m-0 mt-2 text-muted"><small>Billed to</small></p>
                        <p class="m-0 mt-n2 text-muted"><small><b><?= $v["bill_to"] ?></b></small></p>

                      </div>
                      <div class="p-5 mb-2 bg-light col text-right">
                        
                        <p class="m-0 mt-2 text-muted"><small>Reference</small></p>
                        <p class="m-0 mt-n2 text-muted"><small><b>#<?= $v["reference"] ?></b></small></p>

                        <p class="m-0 mt-2 text-muted"><small>Order Ship to</small></p>
                        <p class="m-0 mt-n2 text-muted"><small><b><?= $v["ship_to"] ?></b></small></p>
                      </div>
                    </div>
                    <div class="px-5">
                      <?php foreach($order_details as $k=>$x){ ?>
                      <div class="row m-0">
                        <div class="p-2 m-0 position-relative d-block border-bottom col-8 pack">

                          <div class="pack-action">
                            <?php if(!$x['box_number']){ $count_unbox++; ?>
                              
                              <input 
                                type="checkbox" 
                                class="<?= "check-box-".$slip_id ?>" 
                                name="picking_order_id" 
                                value="<?= $x['stock_id'] ?>" 
                                data-detail="<?= $x["product_description"] ?>" 
                                data-lot="<?= $x["stock_lotno"] ?>"  
                                data-uom="<?= $x["stock_qty"] . " " . $x["unit_name"] ?>" 
                                data-name="<?= $x["product_code"] ?>"
                              />

                              <small>Select item</small>
                            <?php } else { $count_box++; 
                              $total_boxes[] = $x['box_number'];
                              $total_raw_boxes[] = $countedBoxNo = str_replace($v["slip_no"]."-", "", $x['box_number']);
                              ?>
                              <button type="button" onclick="undoBox(<?= $x['stock_id'] ?>,<?= $countedBoxNo ?>)" class="btn btn-sm btn-danger"><small>Undo</small></button>
                              <button class="btn btn-sm btn-primary rounded"><small>@ box <?= $countedBoxNo ?></small></button>
                            <?php } ?>
                          </div>

                          <p class="m-0 mt-2 text-muted"><small><?= $x["product_code"] ?></small></p>
                          <p class="m-0 mt-n2 text-muted text-truncate" data-toggle="tooltip" title="<?= $x["product_description"] ?>"><small><b><?= $x["product_description"] ?></b></small></p>

                        </div>
                        <div class="p-2 m-0 border-bottom col-2">
                          <p class="m-0 mt-2 text-muted"><small>Ln/Sn</small></p>
                          <p class="m-0 mt-n2 text-muted" data-toggle="tooltip" title="<?= $x["stock_lotno"] ?>"><small><b><?= $x["stock_lotno"] ?></b></small></p>
                        </div>
                        <div class="p-2 m-0 border-bottom col-2">
                          <p class="m-0 mt-2 text-muted"><small>Order</small></p>
                          <p class="m-0 mt-n2 text-muted"><small><b><?= $x["stock_qty"] . " " . $x["unit_name"] ?></b></small></p>
                        </div>
                      </div>
                      
                      <?php } 
                      
                      $total_boxes = array_unique($total_boxes);
                      $total_unbox =  $packing->getUnboxitem($slip_id);
                      if(empty($total_raw_boxes)){
                        $total_raw_boxes = array(0);
                      }
                      ?>

                      <div class="row m-0 mt-5">
                        <div class="col col-6 px-0">
                          <button type="button" class="mb-2 btn btn-sm btn-outline-primary box-item" data-max="<?= max($total_raw_boxes) ?>" data-slip="<?= $v["slip_no"] ?>" data-target="<?= "check-box-".$slip_id ?>">Pack Selected Item(s)</button>
                          <button type="button" class="mb-2 btn btn-sm btn-outline-success print-label" data-total="<?= count($total_boxes) ?>" data-target="<?= "check-box-".$slip_id ?>" data-slip_no="<?= $v['slip_no'] ?>" data-ship_to="<?= $v['ship_to'] ?>" data-customer_address="<?= $v['customer_address'] ?>" >Print Label</button>
                          <button type="button" class="mb-2 btn btn-sm btn-outline-success print-box-label" data-target="<?= "check-box-".$slip_id ?>" data-slip_id="<?= $slip_id ?>">Print Box Labels</button>
                          <button type="button" class="mb-2 btn btn-sm btn-outline-danger undo-all" data-target="<?= "check-box-".$slip_id ?>" onclick="undoallBox(<?= $slip_id ?>)">Undo All</button>
                        </div>
                        <div class="col col-6 px-0 text-right">
                          <button type="button" class="mb-2 btn btn-sm btn-outline-danger" onclick="recheckOrder(<?= $slip_id ?>)"  data-target="<?= "check-box-".$slip_id ?>">Return to Checker</button>
                        </div>

                      </div>

                    </div>
                    <button id="btn_send" class="d-block btn btn-primary w-100 mt-5 to-ship" data-total="<?= $total_unbox ?>" data-target="<?= "check-box-".$slip_id ?>" onclick="shipOrder(<?= $slip_id ?>)">Send to shipping</button>
                  </div>
                  </div>
                </div>
            </div>







            <?php } ?>
        </div>
    <?php } else {
        ?>
        <div class="col p-0">
            <div class="card-panel p-4 border-0 empty rounded-lg">
                <p class="m-0 text-muted"><small><?= date("jS \of M Y") ?></small></p>
                <p class="m-0 font-weight-bold">There are no matching orders that matched the scanned document.<br>Please try scanning <a href="packing.php?slip_no="><u>again</u></a></p>
            </div>
        </div>

        <?php
        }

    } ?>
</div>




<div class="modal fade ios" id="boxModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Packing Item(s)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body pb-0">
        <div class="row" id="selected-to-box-gallery"></div>
        <input type="hidden" id="toBox" class="form-control" />
        <input type="hidden" id="toName" class="form-control mb-2" readonly>
        <input type="hidden" id="noBox" class="form-control">
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="boxItem" class="btn btn-primary">Box Item(s)</button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade ios" id="printBoxDetail" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content border-0 rounded-0 shadow">
      
      <div class="modal-header">
        <h5 class="modal-title">Print Box Labels</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body">
        <label>Select Box</label>
        
        <select id="boxSelect" data-slip_id="0" class="form-control custom-selct  mb-2">
          <option value="0" selected disabled>select</option>
        </select>

      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="boxItem" class="btn btn-primary m-print-box-label">Print Labels</button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade ios" id="printModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content border-0 rounded-0 shadow">
      
      <div class="modal-header">
        <h5 class="modal-title">Print Shipping Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body" id="shipping_content">
        
        <input type="hidden" id="slipno" class="form-control mb-2" />
        <input type="hidden" id="shipto" class="form-control mb-2" />
        <input type="hidden" id="caddress" class="form-control mb-2" />

        <label>Courier: </label>
        <select id="courier" class="form-control custom-selct  mb-2">
          <option value="Lalamove">Lalamove</option>
          <option value="Grab">Grab</option>
          <option value="Lex PH">Lex PH</option>
          <option value="Pickup">Pickup</option>
          <option value="Van">Van</option>
          <option value="Transportify">Transportify</option>
          <option value="Sea">Sea</option>
          <option value="Air">Air</option>
        </select>
        <label style="display: none;">No. of Sticker: </label>
        <input style="display: none;" type="number" step="1" min="0" id="page" class="form-control mb-2" value="<?= count($total_boxes) ?>" />
                      
        <label>Remarks: </label>
        <textarea id="remarks" class="form-control mb-2"></textarea>

        <div class="d-none">
        <label>Box weight: </label>
        <input type="number" step="0.1" name="box-weight[]" id="box-weight" class="form-control mb-2 bw" />
        </div>
        
      </div>

      <div class="modal-footer">
        <button type="button" id="printLabel" class="btn btn-primary">Print</button>
      </div>

    </div>
  </div>
</div>

<?php
require_once "./component/footer.php";
