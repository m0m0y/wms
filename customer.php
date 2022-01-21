<?php

require_once "./component/import.php";
$meta_title = 'Home';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";
?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/services/maintenance/customer.js?v=1"></script>
<script src="/wms/services/maintenance/common.js?v=1"></script>


<div class="main-content">
    <div class="row row-cols-1">
        <div class="col">
            <div class="padded mb-5">
                <h1 class="mt-5"><i class="material-icons mr-3">people_outline</i> Our Customers</h1>
                
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active">Customers</li>
                        <li class="breadcrumb-item active" aria-current="page">Unit</li>
                    </ol>
                </nav>

            </div>
        </div>
        <div class="col">
            <div class="input-group mb-4 padded">
                <div class="input-group-prepend">
                    <button class="btn btn-primary rounded-0 add-field" type="button" data-toggle="modal" data-target="#customerModal">Add New Customer</button>
                </div>
                <input id="dataTableSearch" type="search" class="form-control rounded-0 search-field" placeholder="Search here">
            </div>
            <div class="responsive-table">
                <table id="customerTable" class="table bg-white table-bordered">
                    <thead>
                        <th>Name</th>
                        <th>Contact No</th>
                        <th>Address</th>
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

<div class="modal fade" id="customerModal">
    <form action="controller/controller.customer.php?mode=add" method="POST" class="ajax-form" enctype="multipart/form-data" id="customerForm">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">System Users</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="customer_id" name="customer_id">
                    <input required type="text" id="customer_name" name="customer_name" class="form-control rounded-0 mb-3" placeholder="Customer Name">
                    <input required type="text" id="customer_contactno" name="customer_contactno" class="form-control rounded-0 mb-3" placeholder="Contact Number">
                    <input required type="text" id="customer_address" name="customer_address" class="form-control rounded-0 mb-3" placeholder="Complete Address">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="customerDelete">

    <form action="controller/controller.customer.php?mode=delete" method="POST" class="ajax-form" enctype="multipart/form-data" id="customerDeleteForm">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><label id="operation">Delete</label> Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="m-3">You are about to permanently delete a user.<br>Proceed to delete <b id="deleteName">item name</b>'s account ?</p>
                    <input type="hidden" id="customer_id_to_delete" name="customer_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </div>
            </div>
        </div>
    </form>

</div>


<?php
require_once "./component/footer.php";