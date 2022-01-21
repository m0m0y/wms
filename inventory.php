<?php

require_once "./component/import.php";
$meta_title = 'Inventory - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";

?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/services/inventory/inventory.js?v=<?= rand(5,10) ?>"></script>
<script src="/wms/services/maintenance/common.js?v=1"></script>
<div class="main-content">
    <div class="row row-cols-1" id="product-set">

        <div class="col">
            <div class="padded mb-5">
                
                <h1 class="mt-5"><i class="material-icons mr-3">content_paste</i> Inventory Management</h1>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">Inventory</li>
                    </ol>
                </nav>

            </div>
            
            
            <div class="padded">

                <div class="row row-cols-1 row-cols-md-3">
                    <div class="col">
                        <div class="card-panel p-4 ">
                            <p class="m-0 text-muted"><small><?= date('g:ia M j Y') ?></small></p>
                            <p class="m-0 mb-2 font-weight-bold"><small class="font-weight-normal px-2 mr-2 bg-primary text-white">On Rak</small><span id="a-or">0</span></p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card-panel p-4 ">
                            <p class="m-0 text-muted"><small><?= date('g:ia M j Y') ?></small></p>
                            <p class="m-0 mb-2 font-weight-bold"><small class="font-weight-normal px-2 mr-2 bg-warning text-white">In Process</small><span id="a-it">0</span></p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card-panel p-4 ">
                            <p class="m-0 text-muted"><small><?= date('g:ia M j Y') ?></small></p>
                            <p class="m-0 mb-2 font-weight-bold"><small class="font-weight-normal px-2 mr-2 bg-success text-white">In Transit</small><span id="a-ot">0</span></p>
                        </div>
                    </div>
                </div>
                
                <div class="progress mb-4 rounded-0" id="analytic" style="height:15px">
                    <div class="progress-bar" id="m-analytic-rak" role="progressbar" aria-valuemax="100" data-toggle="tooltip" data-placement="bottom" title="On Rak">85%</div>
                    <div class="progress-bar bg-warning" id="m-analytic-cart" role="progressbar" aria-valuemax="100" data-toggle="tooltip" data-placement="bottom" title="On Cart">5%</div>
                    <div class="progress-bar bg-success" id="m-analytic-truck" role="progressbar" aria-valuemax="100" data-toggle="tooltip" data-placement="bottom" title="On Truck">10%</div>
                </div>
            </div>
        
            <div class="input-group mb-4 padded" id="push-search">
                <div class="input-group-prepend">
                    <button class="btn btn-primary rounded-0 add-field" type="button" onclick="btn_upload()"><i class="material-icons myicon-lg">add</i> Upload GRPO</button>
                </div>
                <input id="dataTableSearch" type="search" class="form-control rounded-0 search-field" placeholder="Search here">
            </div>
            
        </div>

        <div class="col">
            <div class="responsive-table">
                
                <table id="productTable" class="table bg-white table-bordered" data-page-length='5'>
                    <thead>
                        <th>Product Code</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>UoM</th>
                        <th>Action</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            
        </div>


    </div>

    <!-- Detailed Lots -->
    
    <div class="row row-cols-1" id="lot-set">

        <div class="col">
            <div class="padded mb-5">
                
                <h1 class="mt-5">Inventory<br><small id="product-to-view">{{product_name}}</small></h1>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active"><i class="material-icons mr-3">keyboard_return</i></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="inventory.php" class="text-primary">Go Back</a></li>
                    </ol>
                </nav>

            </div>
        </div>

        <div class="col">

            <div class="input-group mb-4 mt-1 padded">
                <div class="input-group-prepend">
                    <button class="btn btn-primary rounded-0 add-field" type="button" data-toggle="modal" data-target="#StockModal"><i class="material-icons myicon-lg">add</i> New Stock</button>
                </div>
                <input id="dataTableSearchdetails" type="search" class="form-control rounded-0 search-field" placeholder="Search here">
            </div>

            <div class="padded">
                <div class="row row-cols-1 row-cols-md-3">
                    <div class="col">
                        <div class="card-panel p-4 ">
                            <p class="m-0 text-muted"><small><?= date('g:ia M j Y') ?></small></p>
                            <p class="m-0 mb-2 font-weight-bold"><small class="font-weight-normal px-2 mr-2 bg-primary text-white">On Rak</small><span id="l-or">0</span></p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card-panel p-4 ">
                            <p class="m-0 text-muted"><small><?= date('g:ia M j Y') ?></small></p>
                            <p class="m-0 mb-2 font-weight-bold"><small class="font-weight-normal px-2 mr-2 bg-warning text-white">In Process</small><span id="l-it">0</span></p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card-panel p-4 ">
                            <p class="m-0 text-muted"><small><?= date('g:ia M j Y') ?></small></p>
                            <p class="m-0 mb-2 font-weight-bold"><small class="font-weight-normal px-2 mr-2 bg-success text-white">In Transit</small><span id="l-ot">0</span></p>
                        </div>
                    </div>
                </div>    
            
                <div class="progress mb-4 rounded-0" id="analytic" style="height:15px">
                    <div class="progress-bar" id="analytic-rak" role="progressbar" aria-valuemax="100" data-toggle="tooltip" data-placement="bottom" title="On Rak">85%</div>
                    <div class="progress-bar bg-warning" id="analytic-cart" role="progressbar" aria-valuemax="100" data-toggle="tooltip" data-placement="bottom" title="On Cart">5%</div>
                    <div class="progress-bar bg-success" id="analytic-truck" role="progressbar" aria-valuemax="100" data-toggle="tooltip" data-placement="bottom" title="On Truck">10%</div>
                </div>
            </div>

            <div class="responsive-table">
                
                <table id="productdetailsTable" class="table bg-white table-bordered">
                    <thead>
                        
                        <th>Lot No.</th>
                        <th>Serial</th>
                        <th>Quantity</th>
                        <th>UoM</th>
                        <th>Location</th>
                        <th>Expiration date</th>
                        <th>Action</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
   
        </div>
    </div>
</div>


<div class="modal fade ios" id="StockModal">

    <form action="controller/controller.inventory.php?mode=add" method="POST" class="Stock-form" enctype="multipart/form-data" id="stockForm">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Stock Management</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    
                    <input type="hidden" id="product_id" name="product_id">
                    <input type="hidden" id="stock_id" name="stock_id">

                    <input required type="text" id="product_code" name="product_code" class="form-control rounded-0 mb-3" disabled placeholder="Type Product Code">

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text rounded-0">Qty / <span id="uom">{{UoM}}</span></span>
                        </div>
                        <input required type="number" min="0" id="stock_qty" name="stock_qty" class="form-control rounded-0" placeholder="Quantity">
                    </div>

                    <select required id="rak_id" name="rak_id" class="form-control rounded-0 mb-3"></select>
                    
                    <input required type="text" id="stock_lotno" name="stock_lotno" class="form-control rounded-0 mb-3" placeholder="Type Lot Number">
                    <input type="text" id="stock_serialno" name="stock_serialno" class="form-control rounded-0 mb-3" placeholder="Type Serial Number">

                    <input required type="text" id="reference" name="reference" class="form-control rounded-0 mb-3" placeholder="Type Reference">
                    <input type="text" id="notes" name="notes" class="form-control rounded-0 mb-3" placeholder="Type Notes">
                    <label id="label_exp">Expiration Date:</label>
                    <input type="date" id="stock_expiration_date" name="stock_expiration_date" class="form-control rounded-0 mb-3">
                    <label>Transaction Date:</label>
                    <input required type="date" id="transaction_date" name="transaction_date" class="form-control rounded-0 mb-3">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closedetails()"><i class="material-icons myicon-lg">close</i> Close</button>
                    <button type="submit" class="btn btn-primary"><i class="material-icons myicon-lg">save_alt</i> Save Stock</button>
                </div>
            </div>
        </div>
    </form>

</div>



<div class="modal fade ios" id="UploadModal">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">UPLOAD GRPO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <div class="image-area mx-auto border-0" data-target="grpofile">
                            <div class="content text-center">
                                <p class="text-muted mt-1">Click here to import GRPO</p>
                                <i class="material-icons">publish</i>
                            </div>
                        </div>
                    </div>

                    <form class="upload-form" name="form" method="post" action="controller/controller.inventory.php?mode=upload" enctype="multipart/form-data" style="display: none;">
                        <input required id="grpofile" type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" name="grpofile"/>
                        <button type="submit">Upload</button>
                    </form>
                </div>
            </div>
        </div>


</div>


<?php
require_once "./component/footer.php";
