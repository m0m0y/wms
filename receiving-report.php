<?php

require_once "./component/import.php";
$meta_title = 'Receiving Reports - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";

date_default_timezone_set("Asia/Manila");

$add = Sanitizer::filter('add', 'get');
$validate = Sanitizer::filter('validate', 'get');

if($add) { require_once "receiving-add.php"; exit(); }
if($validate) { require_once "receiving-validate.php"; exit(); }

// require_once "model/model.receiving.php";

// $receiving = new Receiving();
// $received = $receiving->getAllReports();

?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>

<script src="/wms/services/reports/receiving/receiving.js?v=beta-11"></script>

<div class="main-content" id="live">

    <div class="row row-cols-1">
        <div class="col">
            <div class="padded mb-5">
                
                <h1 class="mt-5"><i class="material-icons mr-3">list_alt</i> Receiving Reports</h1>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">Receiving</li>
                    </ol>
                </nav>

            </div>
        </div>  
        
        <div class="col">

            <div class="input-group mb-4 padded">
                <div class="input-group-prepend">
                    <!-- <button class="btn btn-primary rounded-0 add-field redirect" data-href="receiving-report.php?add=true" type="button"><i class="material-icons myicon-lg">add</i> Receiving Report</button> -->
                    <button class="btn btn-primary rounded-0 add-field" type="button" onclick="btn_upload()"><i class="material-icons myicon-lg">add</i> Upload PO</button>
                </div>
                <div class="input-group-prepend">
                    <select id="report_status" class="form-control bg-secondary" style="color: #fff; border-right: 10px solid #6c757d !important;">
                        <option selected="">Pending</option>
                        <option>Finished</option>
                    </select>
                </div>
                <input id="dataTableSearch" type="search" class="form-control rounded-0 search-field" placeholder="Search here">
            </div>
            
        </div>
    </div>

    <div class="row row-cols-1 padded">

        <div class="responsive-table">
            <table id="receivingTable" class="table bg-white table-bordered">
                <thead>
                    
                    <th>Company</th>
                    <th>Reference No.</th>
                    <th>Control No.</th>
                    <th>Time / Date of Delivery</th>
                    <th>Notes</th>
                    <th>Action</th>
                </thead>
                <tbody>
                <!-- <?php
                    if(!empty($received)){
                        foreach($received as $key => $v){ 
                ?>
                    <tr>
                        <td><?= $v['company_name'] ?></td>
                        <td><?= $v['reference'] ?></td>
                        <td><?= $v['control_no'] ?></td>
                        <td><?= $v['delivery'] ?></td>
                        <td>
                            <span class="btn btn-sm bg-muted px-4" style="white-space: nowrap"><?= $v['type'] ?></span>
                            <span class="btn btn-sm bg-muted px-4" style="white-space: nowrap"><?= $v['kind'] ?></span>
                            <span class="btn btn-sm bg-muted px-4" style="white-space: nowrap"><?= $v['expected_weight'] ?></span>
                            
                            <span class="btn btn-sm <?= ($v['report_status']) ? 'btn-primary' : 'btn-warning' ?> px-4" style="white-space: nowrap"><?= ($v['report_status']) ? 'Picked' : 'Pending' ?></span>
                        </td>
                        
                        <td>
                            <a href="?validate=<?= $v['report_id'] ?>" class="btn btn-sm btn-primary px-4">View</a>
                            <button class="btn btn-sm btn-secondary px-4" onclick="finish(<?php echo $v['report_id']; ?>)">Finish</button>    
                        </td>
                    </tr>
                    

                <?php 
                        } 
                    }
                ?> -->
                </tbody>
            </table>
        </div>
        

    </div>

</div>



<div class="modal fade ios" id="UploadModal">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload PO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <div class="image-area mx-auto border-0" data-target="grpofile">
                            <div class="content text-center">
                                <p class="text-muted mt-1">Click here to import PO</p>
                                <i class="material-icons">publish</i>
                            </div>
                        </div>
                    </div>

                    <form class="upload-form" name="form" method="post" action="controller/controller.receiving.php?mode=upload" enctype="multipart/form-data" style="display: none;">
                        <input required id="grpofile" type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" name="grpofile"/>
                        <button type="submit">Upload</button>
                    </form>
                </div>
            </div>
        </div>


</div>

<!-- <script>
    $(document).ready( function () {

        $('#receivingTable').DataTable().destroy();
        $('#receivingTable').DataTable({
            "bLengthChange": false
        });
        $('#dataTableSearch').on('keyup', function(){
            $('#receivingTable').DataTable().search($(this).val()).draw();
        })

    });
</script> -->


<?php
require_once "./component/footer.php";
