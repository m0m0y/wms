<link rel="stylesheet" href="/wms/lib/datatable/datatables.min.css">
<script src="/wms/lib/datatable/datatables.min.js"></script>

<script src="/wms/services/reports/receiving/receiving.js?v=beta-11"></script>
<script src="/wms/static/js/ajax.js?v=beta-11"></script>

<div class="main-content" id="live">

    <div class="row row-cols-1">
        <div class="col">
            <div class="padded mb-5">
                
                <h1 class="mt-5"><i class="material-icons mr-3">toll</i> Report Details</h1>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent pl-0 mb-0">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active">Receiving</li>
                        <li class="breadcrumb-item active" aria-current="page">Add</li>
                    </ol>
                </nav>

            </div>
        </div>
    </div>

    <form action="controller/controller.receiving.php?mode=add" method="POST" class="ajax-form form-lg" enctype="multipart/form-data">
        <div class="row padded">
            <div class="col col-12 col-md-12">
                <button type="button" class="btn btn-warning text-white my-3 px-4 redirect" data-href="receiving-report.php">Go Back</button>
                <div>
                    <label><small class="font-weight-bold text-muted">Company Name</small></label>
                    <input type="text" class="form-control mb-3" required name="company" placeholder="Company Name">
                    <label><small class="font-weight-bold text-muted">Broker / Supplier / Origin</small></label>
                    <textarea class="form-control mb-3" required name="origin"></textarea>
                    <div class="row row-cols-1 row-cols-md-3">
                        <div class="col mb-3">
                            <label><small class="font-weight-bold text-muted">Shipment Details</small></label>
                            <select required name="type" class="custom-select form-control">
                                <option value="0" selected disabled>Select Container</option>
                                <option value="20 Ft">20 Ft.</option>
                                <option value="40 Ft">40 Ft.</option>
                                <option value="LCL">LCL</option>
                            </select>
                        </div>
                        <div class="col mb-3">
                            <label><small class="font-weight-bold text-muted">&nbsp;</small></label>
                            <select required name="kind" class="custom-select form-control">
                                <option value="Importation" selected>Importation</option>
                                <option value="Local Purchase">Local Purchase</option>
                                <option value="Transfer">Transfer</option>
                                <option value="Miscellaneous">Miscellaneous</option>
                            </select>
                        </div>
                        
                        
                        
                        <div class="col mb-3">
                            <label><small class="font-weight-bold text-muted">Control No.</small></label>
                            <input type="text" class="form-control" required name="control_no" placeholder="0">
                        </div>
                        
                        <div class="col mb-3">
                            <label><small class="font-weight-bold text-muted">Reference No.</small></label>
                            <input type="text" class="form-control" required name="ref_no" placeholder="0">
                        </div>
                        
                        <div class="col mb-3">
                            <label><small class="font-weight-bold text-muted">Date of Delivery</small></label>
                            <input type="date" class="form-control" required name="date_delivery" placeholder="0">
                        </div>

                        <div class="col mb-3">
                            <label><small class="font-weight-bold text-muted">Total Weight</small></label>
                            <input type="text" step="1" class="form-control" required name="total_weight">
                        </div>

                        <div class="col mb-3" style="display: none;">
                            <label><small class="font-weight-bold text-muted">No. of Packages</small></label>
                            <input type="number" step="1" class="form-control" name="no_of_packages" placeholder=>
                        </div>

                    </div>
                    
                    <label><small class="font-weight-bold text-muted">Remarks</small></label>
                    <textarea class="form-control mb-3" required name="remarks"></textarea>
                    <label><small class="font-weight-bold text-muted">Disposition Incase of Damage / Discrepancy</small></label>
                    <textarea class="form-control mb-3" required name="disposition"></textarea>
                </div>
            </div>

              
            </div>

            <div class="col col-12 col-md-12" >
                <div id="product-receive">
                    
                    <h1 class="mt-5"><i class="material-icons mr-3">add</i> Items</h1>
                    <div class="form-wrap main-product">
                        <div class="row">
                            <div class="col col-12 col-md-6">
                                <label><small class="font-weight-bold text-muted">Item Code</small></label>

                                  <input class="form-control mb-3 select_item" name="item_code[]" list="browsers" autocomplete="off">
                                  <datalist id="browsers" name="item_codee[]">
                                    
                                  </datalist>
                            </div>
                            
                            <div class="col col-12 col-md-6">
                                <label><small class="font-weight-bold text-muted">Lot No.</small></label>
                                <input class="form-control mb-3" name="item_lot[]" placeholder="Lot number" />  
                            </div>

                            <div class="col col-12 col-md-12">
                                <label><small class="font-weight-bold text-muted">Description</small></label>
                                <input class="form-control mb-3" required name="item_description[]" placeholder="Item description" />  
                            </div>

                            <div class="col col-12 col-md-3">
                                <label><small class="font-weight-bold text-muted">Product Expiry</small></label>
                                <select class="custom-select form-control" required name="item_expiry_month[]">
                                    <option value="jan">Jan</option>
                                    <option value="feb">Feb</option>
                                    <option value="mar">Mar</option>
                                    <option value="apr">Apr</option>
                                    <option value="may">May</option>
                                    <option value="jun">Jun</option>
                                    <option value="jul">Jul</option>
                                    <option value="aug">Aug</option>
                                    <option value="sep">Sep</option>
                                    <option value="oct">Oct</option>
                                    <option value="nov">Nov</option>
                                    <option value="dec">Dec</option>
                                    <option value="n/a">N/A</option>
                                </select>
                            </div>
                            
                            <div class="col col-12 col-md-3">
                                <label><small class="font-weight-bold text-muted">&nbsp;</small></label>
                                <input class="form-control mb-3" type="number" required name="item_expiry_year[]" placeholder="2025" />  
                            </div>

                            <div class="col col-12 col-md-6">
                                <label><small class="font-weight-bold text-muted">Unit</small></label>
                                <select name="item_unit[]" class="form-control mb-3">
                                    
                                </select>
                            </div>

                            <div class="col col-12">
                                <br><br>
                            </div>

                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-primary btn-sm mt-3 mr-3 clone"><i class="material-icons myicon-lg">add</i> Item</button>
                <button type="button" class="btn btn-danger  btn-sm mt-3 remove remove-clone"><i class="material-icons myicon-lg">remove</i> Item</button>
    
                <hr>

                <button type="submit" class="btn btn-success mt-3 ">Save Report</button>

            </div>



        </div>

    </form>

</div>


<?php
require_once "./component/footer.php";