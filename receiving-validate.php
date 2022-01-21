<?php

require_once "./component/import.php";
$meta_title = 'Home';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";

date_default_timezone_set("Asia/Manila");
$validate = Sanitizer::filter('validate', 'get');

if(!$validate) { exit; }

require_once "model/model.receiving.php";
$receiving = new Receiving();

$report = $receiving->getReport($validate);

?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>

<script src="/wms/services/reports/receiving/receiving.js?v=beta-11"></script>
<div class="main-content" id="live">
    <div class="row row-cols-1">
        <div class="col">
            <div class="padded mb-5">
                <h1 class="mt-5"><i class="material-icons mr-3">toll</i> #<?= $report[0]['control_no'] ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">Receiving</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 padded">
        <div class="col row m-0">

            <div class="row m-0 mb-5 border col-12 p-0 col-md-10">

                <div class="col col-12 row m-0 mb-4 bg-light">
                    <div class="col col-12 col-md-5 d-block px-5 pt-5 pb-3 pb-md-5">  
                        <small><b>Company</b></small>
                        <br>
                        <?= $report[0]['company_name'] ?>
                        <br>
                        <small><b>Broker/Supplier/Origin</b></small>
                        <br>
                        <?= $report[0]['origin'] ?>
                    </div>
                    
                    <div class="col col-12 col-md-3 px-5 py-3 py-md-5">  
                        <small><b>Reference</b></small>
                        <br>
                        <?= $report[0]['reference'] ?>
                        <br>
                        <small><b>Time / Date of Delivery</b></small>
                        <br>
                        <?= date_format(date_create($report[0]['delivery']), 'Y-m-d') ?>
                    </div>

                    <div class="col col-12 col-md-4 d-block mb-3 px-5 py-3 py-md-5">  
                        <small><b>Control No.</b></small>
                        <br>
                        <h3><?= $report[0]['control_no'] ?></h3>
                        <span class="btn btn-sm mr-1 mb-1 px-3 bg-muted rounded" style="white-space: nowrap"><?= $report[0]['type'] ?></span>
                        <span class="btn btn-sm mr-1 mb-1 bg-muted rounded" style="white-space: nowrap"><?= $report[0]['kind'] ?></span>
                        <span class="btn btn-sm mr-1 mb-1 bg-muted rounded" style="white-space: nowrap"><?= $report[0]['expected_weight'] ?></span>
                    </div>
                </div>

                <div class="col col-12">
                    <div class="px-5">

                        <button type="button" id="go-back" class="btn btn-warning text-white mb-2 px-4 btn-sm redirect" data-href="receiving-report.php">
                            Go Back
                        </button>
                        
                        <?php if($report[0]["report_status"]){ ?>
                        <button type="button" class="btn btn-primary text-white mb-2 px-4 btn-sm re-rr" data-id="<?= $report[0]["report_id"] ?>">
                            <i class="material-icons myicon-lg mr-2">sync</i>
                            Recount
                        </button>
                        <?php } ?>
                            
                        <button type="button" class="btn btn-success text-white mb-2 px-4 btn-sm redirect" data-href="tcpdf/examples/receiving-report.php?report=<?= $report[0]["report_id"] ?>">
                            Export as PDF
                        </button>
                        
                        <button type="button" class="btn btn-danger text-white mb-2 px-4 btn-sm delete-rr" data-id="<?= $report[0]["report_id"] ?>">
                            Delete
                        </button>
                        
                    </div>
                </div>

                <div class="col col-12">
                    <div class="responsive-table p-5 pt-0 mb-4">
                        <table class="table bg-white table-bordered no-footer">
                            <thead>
                                <tr role="row">
                                    <th><small class="font-weight-bold">Item Code</small></th>
                                    <th><small class="font-weight-bold">Description</small></th>
                                    <th><small class="font-weight-bold">Lot No.</small></th>
                                    <th><small class="font-weight-bold">Expiry</small></th>
                                    <th><small class="font-weight-bold">QTY Received</small></th>
                                    <th><small class="font-weight-bold">Unit</small></th>
                                    <th><small class="font-weight-bold">User Account</small></th>
                                    <th><small class="font-weight-bold">Print</small></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 

                                    if(!empty($report[0]["items"])) {
                                        foreach($report[0]["items"] as $key => $value ) {
                                            
                                            ?>
                                            <tr>
                                                <td><?= $report[0]["items"][$key]["item_code"] ?></td>
                                                <td><?= $report[0]["items"][$key]["item_description"] ?></td>
                                                <td><?= $report[0]["items"][$key]["item_lot"] ?></td>

                                                <td><?= ucfirst($report[0]["items"][$key]["item_expiry_month"]) . " " . $report[0]["items"][$key]["item_expiry_year"] ?></td>
                                                <td><?= $report[0]["items"][$key]["item_received"] ?></td>
                                                <td><?= $report[0]["items"][$key]["item_unit"] ?></td>
                                                <td><?= $report[0]["items"][$key]["user_fullname"] ?></td>

                                                <td><a class="btn btn-sm btn-primary" href="tcpdf/examples/lot.php?stock_lotno=<?= $report[0]["items"][$key]["item_code"].$report[0]["items"][$key]["item_lot"] ?>" target="_blank">
                                                    <i class="material-icons myicon-lg mr-2">print</i>
                                                    Barcode</a></td>

                                            </tr>
                                            <?php
                                        }
                                    }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col col-12 row m-0">
                    
                    <div class="col col-12 col-md-6 px-5 pb-5">  
                        <small><b>Remarks :</b></small>
                        <br>
                        <?= $report[0]['remarks'] ?>
                        <br>
                        <br>
                    </div>
                    <div class="col col-12 col-md-6 px-5 pb-5">  
                        <small><b>Dispostion Incase of damage/discrepancy :</b></small>
                        <br>
                        <?= $report[0]['disposition'] ?>
                        <br>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<?php
require_once "./component/footer.php";
