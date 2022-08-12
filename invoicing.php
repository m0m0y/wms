<?php

require_once "./component/import.php";
$meta_title = 'Order Invoices - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";
require_once "model/model.invoicing.php";

$invoicing = new Invoicing();
$order = $invoicing->getAllOrders();

?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/services/invoicing/invoicing.js?v=2"></script>


<audio id="audio_correct">
  <source src="barcode_sounds/beep_correct.mp3" type="audio/mpeg">
</audio>

<audio id="audio_incorrect">
  <source src="barcode_sounds/beep_incorrect.mp3" type="audio/mpeg">
</audio>

<div class="main-content" id="main">
   <div class="row row-cols-1">

        <div class="col">
            <div class="padded mb-5">
                <h1 class="mt-5">Order Invoices</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">Invoicing</li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <div class="col">
            <div class="row row-cols-1 ml-0 mr-0 padded">
                <div class="col p-0" id="viewing-invoice">
                    <!--  -->
                </div>
            </div>
        </div>

        <div class="col" id="view-trigger">
            <div class="row row-cols-1 row-cols-sm-2 ml-0 mr-0" id="invoice-grid">
                <?php 
                
                if(!empty($order)) {
                foreach($order as $k=>$v) {
                    $slip_id = $v['slip_id'];
                    $slip_no = $v['slip_no']; 
                    $invoice = ($v['invoice_no']!="") ? '<small class="font-weight-normal px-2 mt-1 bg-muted text-black float-right d-inline">Inv No: '.$v['invoice_no'].'</small>' : '';
                    $order_details = $invoicing->getAllOrdersdetails($v['slip_id']);
                    ?>
                    <div class="col view-invoice" data-target="<?= $slip_no ?>">
                        <div class="card-panel p-4">
                            <p class="m-0 text-muted"><small>PO#: <?= $v['po_no'] ?></small></p>
                            <p class="m-0 mb-2 font-weight-bold"><small class="font-weight-normal px-2 mr-2 bg-info text-white">invoicing</small><?= $slip_no ?> <?= $invoice ?></p>
                            <div class="progress mb-3 rounded-0" style="height: 10px;">
                                <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="rounded i-view" style="display: none;" data-target="<?= $slip_no ?>">
                            <div class="r-container">
                                
                                <table class="table table-bordered un-dt">
                                    <thead>
                                        <tr>
                                            <th class="py-4 align-middle text-center">SLIP #: <?= $v['slip_no'] ?></th>
                                            <th  class="py-4 align-middle text-center">INVOICE NO #: <?= $v['invoice_no'] ?></th>
                                            <th class="py-4 align-middle text-center">PO#: <?= $v['po_no'] ?></th>
                                            <th  class="py-4 align-middle text-center">REFERENCE #: <?= $v['reference'] ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="bg-white">
                                            <td colspan="2" class="py-3">
                                                Customer Name:<br>
                                                <?= $v['ship_to'] ?>
                                            </td>
                                            <td colspan="2" class="py-3">
                                                Order Bill to:<br>
                                                <?= $v['bill_to'] ?>
                                            </td>
                                        </tr>
                                        <?php 
                                            foreach ($order_details as $k => $x) { 

                                                $product_id = $x['product_id'];
                                                $order_status = $x['order_status'];
                                                $stock_id = $x['stock_id'];
                                                $product_lots = $invoicing->getAllLots($slip_id,$product_id,"cart");
                                                foreach ($product_lots as $k => $y) { ?>
                                                    <tr class="bg-white">
                                                        <td colspan="1">
                                                            <span><?= $x['product_code'] ?></span>
                                                            <p class="m-0 mt-0 text-muted text-truncate" data-toggle="tooltip" data-placement="top" title="<?= $x['product_description'] ?>"><span><b><?= $x['product_description'] ?></b></span></p>
                                                        </td>
                                                        <td colspan="1">
                                                            <span>Expiration</span>
                                                            <p class="m-0 mt-0 text-muted" data-toggle="tooltip" data-placement="top" title="<?= $y['stock_expiration_date'] ?>"><span><b><?= $y['stock_expiration_date'] ?></b></span></p>
                                                        </td>
                                                        <td colspan="1">
                                                            <span>Ln/Sn</span>
                                                            <p class="m-0 mt-0 text-muted text-truncate" data-toggle="tooltip" data-placement="top" title="<?= $y['stock_lotno'] ?>"><span><b><?= $y['stock_lotno'].' '.$y['stock_serialno'] ?></b></span></p>
                                                        </td>
                                                        <td colspan="1">
                                                            <span>Pk/Or</span>
                                                            <p class="m-0 mt-0 text-muted"><span><b><?= $y['stock_qty'] ?> out of <?= $x['quantity_order'] ?></b> <?= $x['unit_name'] ?></span></p>
                                                        </td>
                                                    </tr>
                                                <?php
                                                }
                                            }
                                            ?>
                                        <tr class="bg-white">
                                            <th colspan="2" class="pt-4">
                                                <button type="button" onclick="repick_order(<?php echo $slip_id; ?>)" class="btn btn-sm py-2 px-3 btn-danger mb-2 mr-2">Repick Order</button>
                                                <button type="button" onclick="print_barcode(<?php echo $slip_id ?>,<?php echo '\''.$slip_no.'\'' ?>)" class="py-2 px-3 btn btn-sm btn-success mb-2 mr-2">Print Barcode</button>
                                                <button onclick="add_invoice(<?php echo $slip_id ?>)" class="btn btn-sm py-2 px-3 btn-info mb-2 mr-2">Add Invoice</button>
                                            </th>
                                            <th colspan="4" class="pt-4">

                                                <?php if (empty($v['invoice_no'])) { ?>
                                                <button class="py-2 px-3 btn btn-sm btn-primary mr-2" disabled>Send to Checker</button>
                                                <?php } else { ?>
                                                <button class="py-2 px-3 btn btn-sm btn-primary mr-2" onclick="check_order(<?php echo $slip_id; ?>)">Send to Checker</button>
                                                <?php } ?>

                                                <button onclick="resetInvoice()" class="btn btn-sm py-2 px-3 btn-secondary mr-2">Go Back</button>
                                                </div>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>

                    </div>
                <?php } } else { ?>
                    <div class="col p-0">

                        <div class="card-panel p-4 border-0 empty rounded-lg">
                            <p class="m-0 text-muted"><small><?= date("jS \of M Y") ?></small></p>
                            <p class="m-0 font-weight-bold">There are no orders to invoice</p>
                        </div>
                
                    </div>
                
                <?php } ?>
            </div>
        </div>
    </div>
</div>



<div class="modal fade ios" id="repickModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label id="operation">Repick Order</label></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="m-3" id="pickingForm">
                    <input type="hidden" id="slip_id" name>
                    <p>Comments <span class="text-danger">*</span></p>
                    <textarea class="form-control mb-3 rounded-0" id="repick_comments"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal"  class="btn btn-secondary">Cancel</button>
                <button type="button" onclick="repick()" class="btn btn-success">Submit</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ios" id="addInvoice">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label id="operation">Invoice Number</label></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="slipid" name>
                <input type="text" id="invoiceno" class="form-control mb-2" placeholder="Input invoice here"/>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal"  class="btn btn-secondary">Cancel</button>
                <button type="button" onclick="save_invoice()" class="btn btn-success">Submit</button>
            </div>
        </div>
    </div>
</div>

<?php
require_once "./component/footer.php";
