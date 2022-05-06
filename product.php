<?php

require_once "./component/import.php";
$meta_title = 'Products - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";

?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/services/maintenance/product.js?v=1"></script>
<script src="/wms/services/maintenance/common.js?v=1"></script>

<div class="main-content">
    <div class="row row-cols-1">
        <div class="col">
            <div class="padded mb-5">

                <h1 class="mt-5"><i class="material-icons mr-3">tune</i>Warehouse Products</h1>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active">Maintenance</li>
                        <li class="breadcrumb-item active" aria-current="page">Products</li>
                    </ol>
                </nav>

            </div>
        </div>
        <div class="col">
            <div class="input-group mb-4 padded">
                <div class="input-group-prepend">
                    <button class="btn btn-primary rounded-0 add-field" type="button" data-toggle="modal" data-target="#productModal"><i class="material-icons myicon-lg">add</i> Add New Product</button>
                </div>
                <input id="dataTableSearch" type="search" class="form-control rounded-0 search-field" placeholder="Search here">
            </div>
            <div class="responsive-table">
                <table id="productTable" class="table bg-white table-bordered">
                    <thead>       
                        <th>Product Code</th>
                        <th>Product Description</th>
                        <th>Unit</th>
                        <th>Product Type</th>
                        <th>Category</th>
                        <th>Product Expiration</th>
                        <th class="md">Action</th>
                    </thead>
                    <tbody>
                        <!-- ajaxial content -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<div class="modal fade ios" id="productModal">
    <form action="" method="POST" class="ajax-form" enctype="multipart/form-data" id="productForm">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Product Management</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="product_id_update" name="product_id">
                    <img id="frame" src="static/default-placeholder.png" class="preview mr-3" onclick="$('#product_image').click()" />
                    <input id="product_image" name="product_image" type="file" onchange="previewImage()" class="invisible" />
                    <div class="ml-3" style="padding-left: 80px;">
                        <label>Product Code</label>
                        <input required type="text" id="product_code" name="product_code" class="form-control rounded-0 mb-3" placeholder="Product Code" data-toggle="tooltip" data-placement="top" title="Product Code">
                        <label>Product Description</label>
                        <input required type="text" id="product_description" name="product_description" class="form-control rounded-0 mb-3" placeholder="Product Description" data-toggle="tooltip" data-placement="top" title="product Description">
                    </div>
                    <label>Product UOM</label>
                    <select required class="form-control mb-3 rounded-0" name="unit_id" id="unit_id"  data-toggle="tooltip" data-placement="top" title="Unit of Measurement">
                        <option value="0" selected="" disabled>Select UoM</option>
                    </select>
                    <label>Product Type</label>
                    <select required class="form-control mb-3 rounded-0" name="product_type" id="product_type" data-toggle="tooltip" data-placement="top" title="Product Type">
                        <option value="track">Track</option>
                        <option value="serial">Serial</option>
                    </select>
                    <label>Product Category</label>
                    <select required class="form-control mb-3 rounded-0" name="category_id" id="category_id" data-toggle="tooltip" data-placement="top" title="Product Category">
                        <option value="0" selected="" disabled>Select Category</option>
                    </select>
                    <label>Expiring</label>
                    <select required class="form-control mb-3 rounded-0" name="product_expiration" id="product_expiration" data-toggle="tooltip" data-placement="top" title="Product Expiry">
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                    <label>Product Weight (Kg)</label>
                    <input required type='number' step='0.0001' value='0.0000' id="product_weight" name="product_weight" class="form-control rounded-0 mb-3" placeholder="0.00" data-toggle="tooltip" data-placement="top" title="Product Weight (kg)">
                    <label>Product Dimensions (L x W x H) (Cm)</label>
                    <div class="input-group">
                        <input required type='number' step='0.0001' value='0.0000' id="product_length" name="product_length" class="form-control rounded-0 mb-3" placeholder="0.00" data-toggle="tooltip" data-placement="top" title="Product Length (cm)">
                        <input required type='number' step='0.0001' value='0.0000' id="product_width" name="product_width" class="form-control rounded-0 mb-3" placeholder="0.00" data-toggle="tooltip" data-placement="top" title="Product Width (cm)">
                        <input required type='number' step='0.0001' value='0.0000' id="product_height" name="product_height" class="form-control rounded-0 mb-3" placeholder="0.00" data-toggle="tooltip" data-placement="top" title="Product Height (cm)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="material-icons myicon-lg">close</i> Close</button>
                    <button type="reset" class="btn btn-danger"><i class="material-icons myicon-lg">clear_all</i> Clear Form</button>
                    <button type="submit" class="btn btn-primary"><i class="material-icons myicon-lg">save</i> Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade ios" id="productDelete">
    <form action="controller/controller.product.php?mode=delete" method="POST" class="ajax-form" enctype="multipart/form-data">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><label id="operation">Delete</label> Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="m-3">You are about to permanently delete an item.<br>Proceed to delete "<b id="deleteName">item name</b>" ?</p>
                    <input type="hidden" id="product_id" name="product_id">
                    <input type="hidden" id="del_product_code" name="del_product_code">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="material-icons myicon-lg">close</i> Close</button>
                    <button type="submit" class="btn btn-danger"><i class="material-icons myicon-lg">delete</i> Delete Item</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
require_once "./component/footer.php";