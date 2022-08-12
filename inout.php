<?php

require_once "./component/import.php";
$meta_title = 'Dashboard - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";

require_once "./model/model.inout.php";

date_default_timezone_set("Asia/Manila");

$inout = new Inout();

$product_code = $inout->getAllProductCodes();
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="/wms/services/maintenance/inout.js?v=1"></script>

<div class="main-content">

    <div class="container-fluid">

        <div class="card mt-5">

            <div class="card-header">
                <legend>Adjustments <span class="badge bg-danger text-white">out</span></legend>
            </div>
            
            <div class="card-body">

                <!-- <div class="row mb-4">
                    <label class="col-sm-2 col-form-label text-right"><span class="text-danger"></span> Search Product:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="search" placeholder="Type Product Code/Product Description" onkeyup="searchValue(this.value)">
                        <div id="textValue"></div>
                    </div>
                </div> -->

                <div class="row mb-4">
                    <label class="col-sm-2 col-form-label text-right"><span class="text-danger">*</span> Products:</label>
                    <div class="col-sm-9">
                        <select class="form-control pcode" name="product_codes" id="product_codes">
                        </select>
                    </div>
                </div>

                <div class="row mb-4">
                    <label class="col-sm-2 col-form-label text-right"><span class="text-danger">*</span> Unit:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="unit" id="unit" placeholder="Unit" readonly>
                    </div>
                </div>

                <div class="row mb-4">
                    <label class="col-sm-2 col-form-label text-right"><span class="text-danger">*</span> Total Items:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="stock_quantity" id="stock_quantity" placeholder="0" readonly>
                    </div>
                </div>

                <div class="row mb-4">
                    <label class="col-sm-2 col-form-label text-right"><span class="text-danger">*</span> Lot Number:</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="lotno" id="lotno"></select>
                    </div>
                </div>

                <div class="row mb-4">
                    <label class="col-sm-2 col-form-label text-right"><span class="text-danger">*</span> Total Items per Lot:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="qty_per_lot" id="qty_per_lot" placeholder="0" readonly>
                    </div>
                </div>

                <div class="row mb-4">
                    <label class="col-sm-2 col-form-label text-right"><span class="text-danger">*</span> Expiration Date:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="exp_date" id="exp_date" placeholder="yyyy-mm-dd" readonly>
                    </div>
                </div>

                <div class="row mb-4">
                    <label class="col-sm-2 col-form-label text-right"><span class="text-danger">*</span> Quantity:</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control" name="quantity" id="quantity" placeholder="Type Here..." onkeyup="quantityVal(this.value)">
                    </div>
                </div>

                <div class="row mb-4">
                    <label class="col-sm-2 col-form-label text-right"><span class="text-danger">*</span> Transaction Date:</label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control" name="transac_date" id="transac_date">
                    </div>
                </div>
                
                <div class="container float-end"><button type="submit" class="btn btn-primary" id="submitBtn">Submit</button></div>
                    
            </div>
        </div>

    </div>

</div>

<script></script>

<?php
require_once "./component/footer.php";