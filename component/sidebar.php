<div class="sidebar <?= (isset($_COOKIE['side-off'])) ? 'off no-transition' : '' ?>">
    <ul class="mb-0" data-simplebar>
        <div class="nav-fix-sace"></div>

        <li>
            <a href="index.php"><i class="material-icons mr-3">insights</i> <span class="s-text">Dashboard</span></a>
        </li>

        <!-- admin -->
        <?php if(in_array($__role, array('admin', 'admin-default'))): ?>
        <li>
            <a href="#!" id="maintenance-trigger" data-target="#maintenance-nav" class="sidebar-dropdown"><i class="material-icons mr-3">tune</i> <span class="s-text">Maintenance</span>
            <i id="maintenance-dropdown-ico" class="material-icons mr-3 dropdown-icon">arrow_drop_down</i></a>
        </li>
        <li class="p-0">
            <ul class="dropdown" id="maintenance-nav">
                <li id="li_unit">
                    <a href="unit.php"><i class="material-icons mr-3">miscellaneous_services</i> <span class="s-text">Unit</span></a>
                </li>
                <li id="li_category">
                    <a href="category.php"><i class="material-icons mr-3">folder</i> <span class="s-text">Category</span></a>
                </li>

                <?php if(in_array($__role, array('admin'))): ?>
                    <li id="li_users">
                        <a href="user.php"><i class="material-icons mr-3">person_outline</i> <span class="s-text">Users</span></a>
                    </li>
                <?php endif ?>

                <li id="li_rak">
                    <a href="rak.php"><i class="material-icons mr-3">layers</i> <span class="s-text">Rak</span></a>
                </li>

                <li id="li_cart">
                    <a href="cart.php"><i class="material-icons mr-3">local_grocery_store</i> <span class="s-text">In Process Location</span></a>
                </li>

                <li id="li_product">
                    <a href="product.php"><i class="material-icons mr-3">inbox</i> <span class="s-text">Product</span></a>
                </li>

                <li id="li_backup">
                    <a href="backup.php"><i class="material-icons mr-3">backup</i> <span class="s-text">Backup Database</span></a>
                </li>

                <li id="li_barcode_generator">
                    <a href="barcode_generator.php"><i class="material-icons mr-3">qr_code_2</i> <span class="s-text">Barcode Generator</span></a>
                </li>

            </ul>
        </li>

        <li>
            <a href="inventory.php"><i class="material-icons mr-3">maps_home_work</i> <span class="s-text">Inventory</span></a>
        </li>

        <li>
            <a href="#!" id="report-trigger" data-target="#inventory_report-nav" class="sidebar-dropdown"><i class="material-icons mr-3">bar_chart</i> <span class="s-text">Reports</span>
            <i id="report-dropdown-ico" class="material-icons mr-3 dropdown-icon">arrow_drop_down</i></a>
        </li>



        <li class="p-0">
            <ul class="dropdown" id="inventory_report-nav">
                
                <li id="li_productReceived">
                    <a href="receiving-report.php"><i class="material-icons mr-3">assignment</i> <span class="s-text">Receiving Report</span></a>
                </li>

                <li id="li_stockcard">
                    <a href="stockcard.php"><i class="material-icons mr-3">assessment</i> <span class="s-text">Stockcard</span></a>
                </li>
                <li id="li_stockAlllot">
                    <a href="stockcardalllot.php"><i class="material-icons mr-3">analytics</i> <span class="s-text">Stocks (All lots)</span></a>
                </li>
                <li id="li_quarantine">
                    <a href="quarantinedItems.php"><i class="material-icons mr-3">storage</i> <span class="s-text">Quarantined Item</span></a>
                </li>
                <li id="li_productExpiry">
                    <a href="product_expiry.php"><i class="material-icons mr-3">assignment</i> <span class="s-text">Product Expiry</span></a>
                </li>
                <li id="li_summary">
                    <a href="summary.php"><i class="material-icons mr-3">article</i> <span class="s-text">Summary</span></a>
                </li>

            </ul>
        </li>
        <?php endif ?>
        <?php if(in_array($__role, array('admin', 'encoder'))): ?>
        <!-- encoders -->
        <li>
            <a href="addorder.php"><i class="material-icons mr-3">loupe</i> <span class="s-text">Add Orders</span></a>
        </li>
        <li>
            <a href="invoicing.php"><i class="material-icons mr-3">payments</i> <span class="s-text">Invoicing</span></a>
        </li>
        <?php endif ?>
        <?php if(in_array($__role, array('admin', 'picker', 'admin-default'))): ?>
        <!-- pickers -->
        <li>
            <a href="picking.php"><i class="material-icons mr-3">qr_code</i> <span class="s-text">Picking</span></a>
        </li>
        <li>
            <a href="receiving.php"><i class="material-icons mr-3">transit_enterexit</i> <span class="s-text">Receiving</span></a>
        </li>
        <?php endif ?>
        <?php if(in_array($__role, array('admin', 'checker'))): ?>
        <li>
            <a href="checking.php?slip_no="><i class="material-icons mr-3">verified</i> <span class="s-text">Checking</span></a>
        </li>
        <?php endif ?>
        <?php if(in_array($__role, array('admin', 'checker', 'packer'))): ?>
        <li>
            <a href="packing.php?slip_no="><i class="material-icons mr-3">business_center</i> <span class="s-text">Packing</span></a>
        </li>
        <?php endif ?>
        <?php if(in_array($__role, array('admin', 'dispatcher', 'packer'))): ?>
        <li>
            <a href="shipping.php"><i class="material-icons mr-3">local_shipping</i> <span class="s-text">Shipping</span></a>
        </li>
        <!-- <li>
            <a href="detrack.php"><i class="material-icons mr-3">gps_fixed</i> <span class="s-text">Detrack</span></a>
        </li> -->
        <li>
            <a href="returned_invoice.php?slip_no="><i class="material-icons mr-3">receipt</i> <span class="s-text">Completed Invoice</span></a>
        </li>
        <?php endif ?>
        <?php if(in_array($__role, array('admin', 'picker'))): ?>
        <li>
            <a href="transfering.php"><i class="material-icons mr-3">shuffle</i> <span class="s-text">Transfering</span></a>
        </li>
        <?php endif ?>
        <?php if(in_array($__role, array('admin', 'admin-default'))): ?>
        <li>
            <a href="transferitem.php"><i class="material-icons mr-3">call_split</i> <span class="s-text">Transfer Item Request</span></a>
        </li>
        <?php endif ?>
        <li>
            <a href="logout.php" class="logout"><i class="material-icons mr-3">exit_to_app</i> <span class="s-text">Logout</span></a>
        </li>

    </ul>

</div>



<div id="confirm-modal" class="modal fade" data-backdrop="static" data-keyboard="false" data-focus="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <div class="modal-body p-5">

                <div class="card-panel p-4 border-0 mb-0 rounded-0 text-cente" data-dismiss="modal">
                    <div class="icon-lg-pop mb-4 text-center">
                        <i class="material-icons text-muted">notifications_none</i>
                    </div>
                    <p class="m-0 font-weight-bold text-center" id="confirm-message">Press Yes to continue</p>
                    <div class="pt-4 text-center">
                        <button id="confirm-ok" class="btn-primary btn" data-callback="true">Ok</button>
                        <button id="confirm-no" class="btn-secondary btn" data-callback="false">Cancel</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>