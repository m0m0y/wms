<?php

require_once "./component/import.php";
$meta_title = 'Unit - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";

?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/services/maintenance/backup.js?v=1"></script>
<script src="/wms/services/maintenance/common.js?v=1"></script>

<div class="main-content">
    <div class="row row-cols-1">
        <div class="col">
            <div class="padded mb-5">
                <h1 class="mt-5"><i class="material-icons mr-3">backup</i> Backup Database</h1>
                
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active">Maintenance</li>
                        <li class="breadcrumb-item active" aria-current="page">Backup Database</li>
                    </ol>
                </nav>

            </div>
        </div>
        <div class="col">
            <div class="input-group mb-4 padded">
                <div class="input-group-prepend">
                    <button class="btn btn-primary rounded-0 add-field" type="button" onclick="backup()" ><i class="material-icons myicon-lg">backup</i> Backup Database Now</button>
                </div>
                <input id="dataTableSearch" type="search" class="form-control rounded-0 search-field" placeholder="Search here">
            </div>
            <div class="responsive-table">
                <table id="databaseTable" class="table bg-white table-bordered">
                    <thead>
                        <th>Database Name</th>
                        <th>Date Generated</th>
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

<?php
require_once "./component/footer.php";