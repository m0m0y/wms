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


<?php
require_once "./component/footer.php";
