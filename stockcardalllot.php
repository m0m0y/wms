<?php

require_once "./component/import.php";
$meta_title = 'Stockcard All Lots - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";

?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<link href="/wms/lib/select/select2.min.css" rel="stylesheet" />
<script src="/wms/lib/select/select2.min.js"></script>
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/services/reports/stockcardall.js?v=1"></script>


<div class="main-content">
  <div class="row row-cols-1" id="product-set">
    <div class="col">
      <div class="padded mb-5">
        <h1 class="mt-5"><i class="material-icons mr-3">list_alt</i> All Lots</h1>
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

      <label class="mt-3 d-block">Generate report fot all product lot numbers: </label>
        
        <select id="d_product" class="form-control rounded-0 select2 mx-500">
          <option value="" selected="" disabled>Select Product</option>
        </select><br>
        <button id="generatePDF" class="btn py-2 px-3 btn-success mt-3"> Generate PDF</button>

            
      </div>
    </div>
  </div>

</div>

<?php
require_once "./component/footer.php";
