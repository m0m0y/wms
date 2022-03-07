<?php

require_once "./component/import.php";
$meta_title = 'In Transit - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";
?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/services/maintenance/cart.js?v=1"></script>
<script src="/wms/services/maintenance/common.js?v=1"></script>

<div class="main-content">
    <div class="row row-cols-1">
        <div class="col">
            <div class="padded mb-5">
                
                <h1 class="mt-5"><i class="material-icons mr-3">tune</i> In Transit Locations</h1>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active">Maintenance</li>
                        <li class="breadcrumb-item active" aria-current="page">In transit</li>
                    </ol>
                </nav>

            </div>
        </div>
        <div class="col">
            <div class="input-group mb-4 padded">
                <div class="input-group-prepend">
                    <button class="btn btn-primary rounded-0 add-field" type="button" data-toggle="modal" data-target="#cartModal"><i class="material-icons myicon-lg">add</i> Add Location</button>
                </div>
                <input id="dataTableSearch" type="search" class="form-control rounded-0 search-field" placeholder="Search here">
            </div>
            <div class="responsive-table">
                <table id="cartTable" class="table bg-white table-bordered">
                    <thead>                    
                        <th>Location Name</th>
                        <th>Location Type</th>
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

<div class="modal fade ios" id="cartModal">
    <form action="controller/controller.cart.php?mode=add" method="POST" class="ajax-form" enctype="multipart/form-data" id="cartForm">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Location Management</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="cart_id" name="cart_id">
                    <input required type="text" id="location_name" name="location_name" class="form-control rounded-0 mb-3" placeholder="Location Name">
                    <select required id="location_type" name="location_type" class="form-control rounded-0">
                        <option value="" selected disabled>-- Select Type --</option>
                        <option value="Cart"> Cart </option>
                        <option value="Pallet"> Pallet </option>
                        <option value="Table"> Table </option>
                        <option value="Tout"> Tout </option>
                        <option value="Cage"> Cage </option>
                        <option value="Truck"> Delivery Truck </option>
                        <option value="Crate"> Crate </option>
                        <option value="Quarantine"> Quarantine </option>
                        <option value="Crate Return"> Crate Return </option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="material-icons myicon-lg">close</i> Close</button>
                    <button type="submit" class="btn btn-primary"><i class="material-icons myicon-lg">save</i> Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade ios" id="cartDelete">

    <form action="controller/controller.cart.php?mode=delete" method="POST" class="ajax-form" enctype="multipart/form-data">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><label id="operation">Deleting</label> Cart</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="m-3">You are about to permanently delete a cart.<br>Proceed to delete <b id="deleteName">item name</b> ?</p>
                    <input type="hidden" id="cart_id_to_delete" name="cart_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="material-icons myicon-lg">close</i> Close</button>
                    <button type="submit" class="btn btn-danger"><i class="material-icons myicon-lg">delete</i> Delete Location</button>
                </div>
            </div>
        </div>
    </form>

</div>



<?php
require_once "./component/footer.php";