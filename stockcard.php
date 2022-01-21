<?php

require_once "./component/import.php";
$meta_title = 'Stockcard - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";

?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<link href="/wms/lib/select/select2.min.css" rel="stylesheet" />
<script src="/wms/lib/select/select2.min.js"></script>
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/services/reports/stockcard.js?v=2"></script>

<div class="main-content">
    <div class="row row-cols-1" id="product-set">
        <div class="col">
            <div class="padded mb-5">
                <h1 class="mt-5"><i class="material-icons mr-3">list_alt</i> Stockcard</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active">Reports</li>
                        <li class="breadcrumb-item active" aria-current="page">Stockcard</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="col">
            <div class="padded mb-5">
                <label class="mt-3 d-block">Product: </label>
                <select id="d_product" class="form-control select2 mx-500">
                    <option value="0" selected disabled>Select Product</option>
                </select>
                <label class="mt-3 d-block">Lot No:</label>
                <select id="d_lotnumber" class="form-control select2 mx-500">
                    <option value="0" selected disabled>Select Lot Number</option>
                </select>
                <br>
                <button id="generatePDF" class="btn btn-success mt-3"> Generate PDF</button>
            </div>
        </div>
    </div>
</div>

<?php
require_once "./component/footer.php";
