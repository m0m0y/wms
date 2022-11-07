<?php

require_once "./component/import.php";
$meta_title = 'Add Orders - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";
?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/services/addorder/addorder.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="main-content full-page">

	<div class="row row-cols-1" id="product-set">
        <div class="col">
            <div class="padded mb-5">
                <h1 class="mt-5"><i class="material-icons mr-3">add</i> Import Orders</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">Add Orders</li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <div class="container">
            <button class="btn btn-md btn-primary" type="button" onclick="addOrdersManual()"><i class="material-icons myicon-lg">edit</i> Add Orders Manual</button>
        </div>
	</div>

    <div class="padded">
        <div class="image-area" data-target="orderfile">
            <div class="content text-center">
                <i class="material-icons">publish</i>
                <p class="text-muted mt-1">Click here to import orders</p>
            </div>
        </div>
    </div>

</div>

<form id="upload-form" name="form" method="post" action="controller/controller.addorder.php?mode=upload" enctype="multipart/form-data" style="display: none;">
	<input required id="orderfile" type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" name="orderfile"/>
	<button type="submit">Upload</button>
</form>


<div class="modal fade ios" id="addOrderForm">
    <form action="controller/controller.addorder.php?mode=addOrderManual" method="POST" class="ajax-form" enctype="multipart/form-data" id="orderForm">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid mb-5">
                        <input type="text" id="slip_no" name="slip_no" class="form-control rounded-0 mb-3" placeholder="Slip Number" data-toggle="tooltip" data-placement="bottom" title="Slip Number" required>

                        <input type="date" id="slip_order_date" name="slip_order_date" class="form-control rounded-0 mb-3" data-toggle="tooltip" data-placement="bottom" title="Order Date" required>

                        <input type="text" id="bill_to" name="bill_to" class="form-control rounded-0 mb-3" placeholder="Customer Name" data-toggle="tooltip" data-placement="bottom" title="Customer Name" required>

                        <input type="text" id="ship_to" name="ship_to" class="form-control rounded-0 mb-3" placeholder="Shipping Address" data-toggle="tooltip" data-placement="bottom" title="Shipping Address" required>

                        <input type="number" id="reference" name="reference" class="form-control rounded-0 mb-3" placeholder="Reference No." data-toggle="tooltip" data-placement="bottom" title="Reference No." required>

                        <input type="text" id="po_no" name="po_no" class="form-control rounded-0 mb-3" placeholder="PO No." data-toggle="tooltip" data-placement="bottom" title="PO No." required>

                        <input type="text" id="address" name="address" class="form-control rounded-0 mb-3" placeholder="Customer Address" data-toggle="tooltip" data-placement="bottom" title="Customer Address" required>

                        <input type="text" id="sales_person" name="sales_person" class="form-control rounded-0 mb-3" placeholder="Sales Person" data-toggle="tooltip" data-placement="bottom" title="Sales Person" required>

                        <input type="text" id="ship_via" name="ship_via" class="form-control rounded-0 mb-3" placeholder="Ship Via" data-toggle="tooltip" data-placement="bottom" title="Ship Via" required>

                        <input type="date" id="ship_date" name="ship_date" class="form-control rounded-0 mb-3" placeholder="Ship Date" data-toggle="tooltip" data-placement="bottom" title="Ship Date" required>

                        <textarea  id="remarks" name="remarks" class="form-control" cols="20" rows="5" placeholder="Type your remarks here..." data-toggle="tooltip" data-placement="bottom" title="Remarks"></textarea>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label text-right"><span class="text-danger">*</span> Products:</label>
                        <div class="col-sm-10">
                            <select class="form-control pcode" style="width: 100%" name="product_codes" id="product_codes"></select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label text-right"><span class="text-danger">*</span> Order qty:</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" name="order_qty" id="order_qty" placeholder="Quantity" data-toggle="tooltip" data-placement="bottom" title="Order Quantity">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label text-right"><span class="text-danger">*</span> Lot No:</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="lotno" id="lotno"></select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label text-right">Location:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="location" id="location" placeholder="Location" data-toggle="tooltip" data-placement="bottom" title="Location">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <label class="col-sm-2 col-form-label text-right"></label>
                        <div class="col-sm-10">
                            <div class="order-container"></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-sm btn-success m-1" id="preview_btn"><i class="material-icons myicon-lg">remove_red_eye</i> Preview</button>
                        <button type="button" class="btn btn-sm btn-danger m-1" onclick="clr_btn()"><i class="material-icons myicon-lg">remove_circle_outline</i> Clear</button>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="addorder_btn">Add Order</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
require_once "./component/footer.php";
