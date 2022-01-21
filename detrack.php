<?php

require_once "./component/import.php";
$meta_title = 'Shipping - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";
require_once "model/model.detrack.php";

$detrack = new Detrack();
$users = $detrack->getAllusers();

?>

<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>
<script src="/wms/lib/jquery/scanner.js"></script>

<script src="/wms/services/detrack/detrack.js?v=beta-11"></script>

<audio id="audio_correct">
  <source src="barcode_sounds/beep_correct.mp3" type="audio/mpeg">
</audio>

<audio id="audio_incorrect">
  <source src="barcode_sounds/beep_incorrect.mp3" type="audio/mpeg">
</audio>

<div class="user-picking" id="user-picking">
    <!-- ajax -->
</div>


<div class="main-content" id="live">
    <div class="row row-cols-1" id="product-set">
        <div class="col">
            <div class="padded mb-5">
                
                <h1 class="mt-5"><i class="material-icons mr-3">gps_fixed</i> Detrack</h1>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">Detrack</li>
                    </ol>
                </nav>

            </div>
        </div>
    </div>
<div class="col">
    <div class="input-group mb-4 padded">
        <div class="input-group-prepend">
            <button class="btn btn-primary rounded-0 add-field" type="button" data-toggle="modal" data-target="#categoryModal" disabled=""><i class="material-icons myicon-lg">search</i></button>
        </div>
        <input id="dataTableSearch" type="search" class="form-control rounded-0 search-field" placeholder="Search here">
    </div>
    <div class="responsive-table">
        <table id="detrackTable" class="table bg-white table-bordered">
            <thead>
                <th></th>
                <th>Type</th>
                <th>Slip No #</th>
                <th>Date</th>
                <th>Address</th>
                <th>Tracking #</th>
                <th>Tracking Status</th>
                <th>Deliver to / Collect from</th>
                <th>Assign to</th>
                <th>Status</th>
                <th>Time</th>
                <th>Reject</th>
                <th>Reason</th>
                <th>Received by</th>
                <th></th>
            </thead>
            <tbody>
                <!-- ajaxial content -->
            </tbody>
        </table>
    </div>
</div>
<br>
<div class="col" style="margin-bottom: 100px">
    <iframe id="gmap" width="100%" height="500" src=""></iframe>
    <br><button id="gmapclose" class="btn btn-sm btn-primary">Close Map</button>
</div>
    
<div class="col" style="margin-bottom: 100px">
    <h2>Locate Online Users</h2><br><br>
    <?php foreach($users as $k=>$v) { ?>
    <?php
        $user_location = unserialize($v['location']);
        $lat = $user_location['lat'];
        $long = $user_location['long'];
    ?>
        <h5><i class="material-icons myicon-lg" style="color:mediumseagreen">fiber_manual_record</i> <span style="cursor: pointer" onclick="viewLocation(<?= $lat ?>,<?= $long ?>)"><u><?= $v['user_fullname']; ?></u></span></h5>
    <?php } ?>
</div>

</div>

<div class="modal fade ios" id="user_modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title">User's Location</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <iframe id="gmapuser" width="100%" height="500" src=""></iframe>
            </div>

            <div class="modal-footer">
            </div>
            
        </div>
    </div>
</div>

<div class="modal fade ios" id="image_modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title">Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <img src="" id="image_order" style="width: 100%;height: 500px">
            </div>

            <div class="modal-footer">
            </div>
            
        </div>
    </div>
</div>

<input type="hidden" id="viewcache_id" />
<input type="hidden" id="viewcache_name" />
<input type="hidden" id="viewcache_no" />


<?php

    /* preload view if url param exist */
    $__i = Sanitizer::filter('i', 'get');
    $__n = Sanitizer::filter('n', 'get');
    $__m = Sanitizer::filter('m', 'get');
    $__c = Sanitizer::filter('c', 'get');
    $__t = Sanitizer::filter('t', 'get');

    if($__i && $__n && $__m) {
        ?>
        <script>
            $(function(){
                pickOrder('<?= $__i ?>', '<?= $__n ?>', '<?= $__m ?>', '<?= $__c ?>')
            })
        </script>
        <?php
    }
    
    /* toast a message if url param exist */

    if($__t) {
        ?>
        <script>
            $.Toast("<?= $__t ?>", {
                'duration': 4000,
                'position': 'top',
                'align': 'left',
            });
            window.history.replaceState(null, null, window.location.pathname);
        </script>
        <?php
    }

?>
<?php
require_once "./component/footer.php";