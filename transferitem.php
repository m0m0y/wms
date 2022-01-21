<?php

require_once "./component/import.php";
$meta_title = 'Transfer Item Requests - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";

?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/services/transfer/transfer.js?v=1"></script>


<div class="main-content">

    <div class="row row-cols-1" id="product-set">
        <div class="col">
            <div class="padded mb-5">
                <h1 class="mt-5"><i class="material-icons mr-3">swap_horizontal_circle</i> Transfer Item Requests</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">Transfer Item Request</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="col px-0">
        <div class="input-group mb-4 padded">
            <div class="input-group-prepend">
                <button class="btn btn-secondary rounded-0 add-field" disabled="">Search</button>
            </div>
            <input id="dataTableSearch" type="search" class="form-control rounded-0 search-field" placeholder="Search here">
        </div>
        <div class="responsive-table">
            <table id="transferTable" class="table bg-white table-bordered">
                <thead>
                    <th>Product Code</th>
                    <th>Product Description</th>
                    <th>Location</th>
                    <th>Quantity Moving</th>
                    <th>Moving To</th>
                    <th>Requested By</th>
                    <th>Status</th>
                    <th class="md">Action</th>
                </thead>
                <tbody>
                    <!-- ajaxial content -->
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade ios" id="request" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="to-break"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label>Quantity to Move</label>
                    <input required type="number" id="qty" class="form-control rounded-0 mb-3">
                    <input type="hidden" id="req_id" />
                    <label>Move to</label>
                    <select required id="raks" name="rak_id" class="form-control rounded-0 mb-3">
                        <!-- ajax raks -->
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="breakdown()">Request Transfer</button>
                </div>
            </div>
        </div>
    </div>  



</div>

<?php
require_once "./component/footer.php";
