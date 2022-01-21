<?php

require_once "./component/import.php";
$meta_title = 'Summary - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";

?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/services/reports/summary.js?v=1"></script>
<div class="main-content">
    <div class="row row-cols-1" id="product-set">
        <div class="col">
            <div class="padded mb-5">
                <h1 class="mt-5"><i class="material-icons mr-3">list_alt</i> Summary</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active">Reports</li>
                        <li class="breadcrumb-item active" aria-current="page">Summary</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="col">
            <div class="padded mb-5 mx-500">
                <div class="input-group mb-3">
                    <input type="text" class="form-control p-none" placeholder="Generate Overall Report">
                    <div class="input-group-append">
                        <button id="overall_products" class="btn-primary input-group-text">Export to Excel</button>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control p-none" placeholder="Generate report for Products with LOT">
                    <div class="input-group-append">
                        <button id="overall_products_withlots" class="btn-primary input-group-text">Export to Excel</button>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<?php
require_once "./component/footer.php";
