<?php

require_once "./component/import.php";
$meta_title = 'Quarantined Items - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";

?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/services/reports/quarantine.js?v=1"></script>
<div class="main-content">
    <div class="row row-cols-1" id="product-set">
        <div class="col">
            <div class="padded mb-5">
                <h1 class="mt-5"><i class="material-icons mr-3">list_alt</i> Quarantined Items</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active">Reports</li>
                        <li class="breadcrumb-item active" aria-current="page">Quarantined Item</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="col">
            <div class="padded mb-5">
                <label>Date From:</label>
                <input class="form-control mx-500 mb-2 rounded-0" type="date" id="dateFrom" />
                <label>Date To:</label>
                <input class="form-control mx-500 mb-2 rounded-0" type="date" id="dateTo" />
                <button id="generatePDF" class="btn px-3 py-2 btn-success mt-3"> Generate PDF</button>
            </div>
        </div>
    </div>
</div>

<?php
require_once "./component/footer.php";
