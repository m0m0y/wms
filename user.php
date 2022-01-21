<?php

require_once "./component/import.php";
$meta_title = 'System Users - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";
?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/services/maintenance/user.js?v=1"></script>
<script src="/wms/services/maintenance/common.js?v=1"></script>

<div class="main-content">
    <div class="row row-cols-1">
        <div class="col">
            <div class="padded mb-5">
                <h1 class="mt-5"><i class="material-icons mr-3">tune</i> System Users</h1>
                
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active">Maintenance</li>
                        <li class="breadcrumb-item active" aria-current="page">User</li>
                    </ol>
                </nav>

            </div>
        </div>
        <div class="col">
            <div class="input-group mb-4 padded">
                <div class="input-group-prepend">
                    <button class="btn btn-primary rounded-0 add-field" type="button" data-toggle="modal" data-target="#userModal"><i class="material-icons myicon-lg">add</i> Add New User</button>
                </div>
                <input id="dataTableSearch" type="search" class="form-control rounded-0 search-field" placeholder="Search here">
            </div>
            <div class="responsive-table">
                <table id="userTable" class="table bg-white table-bordered">
                    <thead>
                            
                        <th>Fullname</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Usertype</th>
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

<div class="modal fade ios" id="userModal">
    <form action="controller/controller.user.php?mode=add" method="POST" class="ajax-form" enctype="multipart/form-data" id="userForm">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">System Users</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="user_id" name="user_id">
                    <input required type="text" id="user_username" name="user_username" class="form-control rounded-0 mb-3" placeholder="User Name">
                    <input required type="text" id="user_fullname" name="user_fullname" class="form-control rounded-0 mb-3" placeholder="Full Name">
                    <input required type="text" id="user_password" name="user_password" class="form-control rounded-0 mb-3" placeholder="Password">
                    <select class="custom-select form-control" id="user_usertype" name="user_usertype" >
                        <option value="admin">Super Administrator</option>
                        <option value="admin-default">Administrator</option>
                        <option value="viewer">Viewer</option>
                        <option value="encoder">Encoder</option>
                        <option value="picker">Picker</option>
                        <option value="checker">Checker</option>
                        <option value="dispatcher">Dispatcher</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="material-icons myicon-lg">close</i> Close</button>
                    <button type="submit" class="btn btn-primary"><i class="material-icons myicon-lg">save_alt</i> Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade ios" id="userDelete">

    <form action="controller/controller.user.php?mode=delete" method="POST" class="ajax-form" enctype="multipart/form-data">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><label id="operation">Delete</label> User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="m-3">You are about to permanently delete a user.<br>Proceed to delete <b id="deleteName">item name</b>'s account ?</p>
                    <input type="hidden" id="user_id_to_delete" name="user_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="material-icons myicon-lg">close</i> Close</button>
                    <button type="submit" class="btn btn-danger"><i class="material-icons myicon-lg">delete</i> Delete User</button>
                </div>
            </div>
        </div>
    </form>

</div>




<?php
require_once "./component/footer.php";