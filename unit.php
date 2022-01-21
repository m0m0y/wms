<?php

require_once "./component/import.php";
$meta_title = 'Unit - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";

?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/services/maintenance/unit.js?v=1"></script>
<script src="/wms/services/maintenance/common.js?v=1"></script>

<div class="main-content">
    <div class="row row-cols-1">
        <div class="col">
            <div class="padded mb-5">
                <h1 class="mt-5"><i class="material-icons mr-3">tune</i> Item Units</h1>
                
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active">Maintenance</li>
                        <li class="breadcrumb-item active" aria-current="page">Unit</li>
                    </ol>
                </nav>

            </div>
        </div>
        <div class="col">
            <div class="input-group mb-4 padded">
                <div class="input-group-prepend">
                    <button class="btn btn-primary rounded-0 add-field" type="button" data-toggle="modal" data-target="#unitModal"><i class="material-icons myicon-lg">add</i> Add New Unit</button>
                </div>
                <input id="dataTableSearch" type="search" class="form-control rounded-0 search-field" placeholder="Search here">
            </div>
            <div class="responsive-table">
                <table id="unitTable" class="table bg-white table-bordered">
                    <thead>
                        <th>Unit Name</th>
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

<div class="modal fade ios" id="unitModal">

    <form action="controller/controller.unit.php?mode=add" method="POST" class="ajax-form" enctype="multipart/form-data" id="unitForm">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Item Units</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="unit_id" name="unit_id">
                    <input required type="text" id="unit_name" name="unit_name" class="form-control rounded-0" placeholder="Type Unit Name here">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="material-icons myicon-lg">close</i> Close</button>
                    <button type="submit" class="btn btn-primary"><i class="material-icons myicon-lg">save_alt</i> Save changes</button>
                </div>
            </div>
        </div>
    </form>

</div>

<div class="modal fade ios" id="unitDelete">

    <form action="controller/controller.unit.php?mode=delete" method="POST" class="ajax-form" enctype="multipart/form-data">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><label id="operation">Delete</label> Unit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="m-3">You are about to permanently delete an item.<br>Proceed to delete "<b id="deleteName">item name</b>" ?</p>
                    <input type="hidden" id="unit_id_to_delete" name="unit_id">
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