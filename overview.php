<?php

require_once "./component/import.php";
$meta_title = 'System Users - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";
?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>

<div class="main-content">
    <div class="row row-cols-1">
        <div class="col">
            <div class="padded mb-5">
                <h1 class="mt-5"><i class="material-icons mr-3">person_outline</i> System Users</h1>
                
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active">Dashboard</li>
                        <li class="breadcrumb-item active" aria-current="page">Overview</li>
                    </ol>
                </nav>

            </div>
        </div>

        <div class="col">
            <div class="padded mb-5">
                <div class="timeline-card p-5">
                    <p class="timeline-title">Add Order</p>
                    <p class="timeline-detail">Slip order <span class="btn btn-sm bg-light mx-2">#0E1233421</span> was added by <span class="btn btn-sm bg-light mx-2">encoder</span>.</p>
                </div>
                
                <div class="timeline-card p-5">
                    <p class="timeline-title">Picking</p>
                    <p class="timeline-detail">Slip order <span class="btn btn-sm bg-light mx-2">#0E1233421</span> was assigned to <span class="btn btn-sm bg-light mx-2">@jpneey</span>.</p>
                    <p class="timeline-detail"><span class="btn btn-sm bg-light mr-2">@jpneey</span> finished picking all items.</p>
                </div>

                <div class="timeline-card p-5">
                    <p class="timeline-title">Invoicing</p>
                    <p class="timeline-detail">Slip order <span class="btn btn-sm bg-light mx-2">#0E1233421</span> was forwarded to the <span class="btn btn-sm bg-light mx-2">encoder</span>.</p>
                    <p class="timeline-detail"><span class="btn btn-sm bg-light mr-2">Encoder</span> finished checking and invoicing all items.</p>
                    <p class="timeline-detail">Invoice with barcode was sent by <span class="btn btn-sm bg-light mx-2">Encoder</span> to <span class="btn btn-sm bg-light mx-2">Checker</span>.</p>
                </div>

                <div class="timeline-card p-5">
                    <p class="timeline-title">Packing</p>
                    <p class="timeline-detail"><span class="btn btn-sm bg-light mr-2">Packer</span> Slip order <span class="btn btn-sm bg-light mx-2">#0E1233421</span> was assigned to <span class="btn btn-sm bg-light mx-2">@jpneey</span>.</p>
                    <p class="timeline-detail"><span class="btn btn-sm bg-light mr-2">@jpneey</span> finished picking all items.</p>
                </div>

                <div class="d-block pt-5 timeline-card-last">
                    
                </div>

            </div>
        </div>
        

    </div>
</div>




<?php
require_once "./component/footer.php";