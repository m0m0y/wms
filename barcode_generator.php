<?php

require_once "./component/import.php";
$meta_title = 'Barcode Generator - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";

?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/services/maintenance/barcode.js?v=2"></script>
<script src="/wms/services/maintenance/common.js?v=1"></script>

<div class="main-content">
    <div class="row row-cols-1">
        <div class="col">
            <div class="padded mb-5">
                <h1 class="mt-5"><i class="material-icons mr-3">qr_code_2</i> Barcode Generator</h1>
                
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active">Maintenance</li>
                        <li class="breadcrumb-item active" aria-current="page">Barcode Generator</li>
                    </ol>
                </nav>

            </div>
        </div>
        <div class="col">
            <input type="text" id="barcode" class="form-control" style="max-width: 600px;" onkeyup="var start = this.selectionStart; var end = this.selectionEnd; this.value = this.value.toUpperCase(); this.setSelectionRange(start, end);" placeholder="Enter value to convert to barcode">
            <button type="button" onclick="generateBarcode()" class="btn py-2 px-4 mt-3 btn-primary">Generate Barcode</button>
        </div>

    </div>
</div>

<?php
require_once "./component/footer.php";