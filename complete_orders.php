<?php

require_once "./component/import.php";
$meta_title = 'Complete Orders - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";
require_once "model/model.picking.php";

$picking = new Picking();
?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/services/reports/complete_orders.js?v=<?= rand(5,10) ?>"></script>
<script src="/wms/services/maintenance/common.js?v=1"></script>

<div class="main-content">
    <div class="row row-cols-1" id="product-set">
        <div class="col">

            <div class="padded mb-5">
                <h1 class="mt-5"><i class="material-icons mr-3">playlist_add_check</i> Finished Orders</h1>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">Complete Orders</li>
                    </ol>
                </nav>
            </div>

            <div class="padded">
                <div class="row row-cols-2 row-cols-md-3">
                    <div class="col-md-3">
                        <div class="card-panel p-3 ">
                            <h4 class="mb-2 font-weight-bold"><span id="pick">0</span></h4>
                            <p class="m-0 text-muted"><small class="font-weight-normal px-2 mr-2 bg-warning text-white"></small> <small>For picking</small></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-panel p-3 ">
                            <h4 class="mb-2 font-weight-bold"><span id="invoice">0</span></h4>
                            <p class="m-0 text-muted"><small class="font-weight-normal px-2 mr-2 bg-info text-white"></small> <small>Invoicing</small></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-panel p-3 ">
                            <h4 class="mb-2 font-weight-bold"><span id="pack">0</span></h4>
                            <p class="m-0 text-muted"><small class="font-weight-normal px-2 mr-2 bg-success text-white"></small> <small>Packing</small></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-panel p-3 ">
                            <h4 class="mb-2 font-weight-bold"><span id="deliver">0</span></h4>
                            <p class="m-0 text-muted"><small class="font-weight-normal px-2 mr-2 bg-info text-white"></small> <small>For shipping</small></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="input-group mb-4 padded" id="push-search">
                <input id="dataTableSearch" type="search" class="form-control rounded-0 search-field" placeholder="Search here">
            </div>

        </div>

        <div class="col">
            <div class="responsive-table">
                
                <table id="finishedOrderTable" class="table bg-white table-bordered" data-page-length='5'>
                    <thead>
                        <th>Slip No</th>
                        <th>Customer Name</th>
                        <th>Ship Address</th>
                        <th>PO No</th>
                        <th>Ship Date</th>
                        <th>Invoice No</th>
                        <th>Status</th>
                        <th>Action</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            
        </div>

        <div class="modal fade ios" id="orderSummaryModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="conatiner mx-2">
                            <h1 class="my-3 text-center">Order Details</h1>
                            
                            <div class="row pt-4 pb-3" style="border-bottom: 1px solid #0000002e;">
                                <div class="col" style="border-right: 1px solid #00000055;">
                                    <h6 class="m-0 text-muted font-weight-normal text-center">Slip No</h6>
                                </div>
                                <div class="col" style="border-right: 1px solid #00000055;">
                                    <h6 class="m-0 text-muted font-weight-normal text-center">Invoice No</h6>
                                </div>
                                <div class="col" style="border-right: 1px solid #00000055;">
                                    <h6 class="m-0 text-muted font-weight-normal text-center">Order date</h6>
                                </div>
                                <div class="col">
                                    <h6 class="m-0 text-muted font-weight-normal text-center">Customer Name</h6>
                                </div>
                            </div>

                            <div class="row py-3" style="border-bottom: 1px solid #0000002e; align-items: center;">
                                <div class="col">
                                    <p class="m-0 text-center" id="sn"></p>
                                </div>
                                <div class="col">
                                <p class="m-0 text-center" id="in"></p>
                                </div>
                                <div class="col">
                                    <p class="m-0 text-center" id="od"></p>
                                </div>
                                <div class="col">
                                    <p class="m-0 text-center" id="c_name"></p>
                                </div>
                            </div>

                            <div class="row pt-4" style="border-bottom: 1px solid #0000002e;">
                                <div class="col text-center" id="image"></div>
                                <div class="col" id="details"></div>
                                <div class="col text-right" id="quantity"></div>
                            </div>

                            <div class="row pt-4">
                                <div class="col text-center">
                                    <p class="m-0 text-muted">Ship date:</p>
                                    <span id="ship_date"></span>
                                </div>

                                <div class="col text-center">  
                                    <p class="m-0 text-muted">Ship Address:</p>
                                    <span id="add"></span>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal"  class="btn btn-secondary">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
require_once "./component/footer.php";