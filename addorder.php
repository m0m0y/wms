<?php

require_once "./component/import.php";
$meta_title = 'Add Orders - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";

require_once "./model/model.addorder.php";

$products = new AddOrder();
$product_code = $products->getAllProductCodes();
?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/services/addorder/addorder.js"></script>

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
                        <input type="text" id="slip_no" name="slip_no" class="form-control rounded-0 mb-3" placeholder="Type Slip Number here" required>

                        <input type="date" id="slip_order_date" name="slip_order_date" class="form-control rounded-0 mb-3" required>

                        <input type="text" id="bill_to" name="bill_to" class="form-control rounded-0 mb-3" placeholder="Type Bill To here" required>

                        <input type="text" id="ship_to" name="ship_to" class="form-control rounded-0 mb-3" placeholder="Type Ship To here" required>

                        <input type="number" id="reference" name="reference" class="form-control rounded-0 mb-3" placeholder="Type Reference No. here" required>

                        <input type="number" id="po_no" name="po_no" class="form-control rounded-0 mb-3" placeholder="Type PO No. here" required>

                        <input type="text" id="address" name="address" class="form-control rounded-0 mb-3" placeholder="Type Address here" required>

                        <input type="text" id="sales_person" name="sales_person" class="form-control rounded-0 mb-3" placeholder="Type Sales Person here" required>

                        <input type="text" id="ship_via" name="ship_via" class="form-control rounded-0 mb-3" placeholder="Type Ship Via here" required>

                        <input type="date" id="ship_date" name="ship_date" class="form-control rounded-0 mb-3" placeholder="Type Ship Date here" required>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label text-right"><span class="required"></span> Search Product:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="search" placeholder="Type Product Code/Product Description" onkeyup="searchValue(this.value)">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label text-right"><span class="required">*</span> Products:</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="product_codes" id="product_codes" required></select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label text-right"><span class="required">*</span> Order qty:</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" name="order_qty" id="order_qty" placeholder="Type Order Quantity here" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label text-right">Lot No:</label>
                        <div class="col-sm-10">
                            <!-- <select class="form-control" name="lotno" id="lotno"></select> -->
                            <input type="text" class="form-control" name="lotno" id="lotno" placeholder="Type Lot Number here" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label text-right"><span class="required">*</span> Location:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="location" id="location" placeholder="Type Location here">
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="material-icons myicon-lg">close</i> Close</button>
                    <button type="submit" class="btn btn-primary"><i class="material-icons myicon-lg">save_alt</i> Add Order</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
require_once "./component/footer.php";
