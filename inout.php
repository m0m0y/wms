<?php

require_once "./component/import.php";
$meta_title = 'Dashboard - Warehouse Management System';
require_once "./component/header.php";
require_once "./component/navbar.php";
require_once "./component/sidebar.php";

require_once "./model/model.inout.php";

date_default_timezone_set("Asia/Manila");

$inout = new Inout();

$product_code = $inout->getAllProductCodes();
?>

<div class="main-content" id="live">

    <div class="container-fluid">

        <div class="card mt-5">

            <div class="card-header">
                <legend><i class="material-icons mr-3"></i> Adjustments <span class="badge bg-danger text-white">out</span></legend>
            </div>
            
            <div class="card-body">

             
                <form method="post" action="controller/controller.inout.php?mode=updateQuantity">

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label text-right"><span class="required"></span> Search Code:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="search" placeholder="Search here.." onkeyup="searchValue(this.value)">
                            <div id="textValue"></div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label text-right"><span class="required">*</span> Product Code:</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="product_codes" id="product_codes">
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label text-right"><span class="required">*</span> Unit:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="unit" id="unit" placeholder="Unit" readonly>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label text-right"><span class="required">*</span> Total Items:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="stock_quantity" id="stock_quantity" placeholder="0" readonly>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label text-right"><span class="required">*</span> Lot Number:</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="lotno" id="lotno"></select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label text-right"><span class="required">*</span> Total Items per Lot:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="qty_per_lot" id="qty_per_lot" placeholder="0" readonly>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label text-right"><span class="required">*</span> Expiration Date:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="exp_date" id="exp_date" placeholder="yyyy-mm-dd" readonly>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label text-right"><span class="required">*</span> Quantity:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="quantity" id="quantity" placeholder="Type Here...">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label text-right"><span class="required">*</span> Transaction Date:</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" name="transac_date" id="transac_date">
                        </div>
                    </div>
                    
                    <div class="container float-end"><button type="submit" class="btn btn-primary">Submit</button></div>
                    
                </form>

                    
            </div>
        </div>

    </div>

</div>

<script>
    
    $(function() {
        $('#product_codes').load('controller/controller.inout.php?mode=getAllProductCode');
    });

    function searchValue(str) {
        if(str.length == 0) {
            $('#product_codes').load('');
            $('#unit').val("");
            $('#stock_quantity').val("");
            $('#qty_per_lot').val("");
            $('#exp_date').val("");
            $('#lotno').load('');
            $('#transac_date').val('');
            return;
        } else {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var responseVal = this.responseText;

                    var obj = $.parseJSON(responseVal);
                    $('#product_codes').load('controller/controller.inout.php?mode=getProductCode&product_code='+obj.product_code);

                    $.ajax({
                        url: 'controller/controller.inout.php?mode=searchProductUnit',
                        method: 'POST',
                        data: {
                            product_code:obj.product_code
                        },
                        success:function(data) {
                            var obj = $.parseJSON(data);

                            $('#unit').val(obj.unit);

                            $('#lotno').load('controller/controller.inout.php?mode=getLotnumber&product_id='+obj.id);
                            var lotno = $('#lotno option:selected').val();

                            if(lotno != "") {
                                $('#qty_per_lot').val("");
                                $('#exp_date').val("");
                            }

                            if(obj.quantity == null){
                                $('#stock_quantity').val(0);
                            } else {
                                $('#stock_quantity').val(obj.quantity);
                            }
                        }
                    });
                }
            }
            xmlhttp.open("GET", "controller/controller.inout.php?mode=searchCode&product_code="+str, true);
            xmlhttp.send();
        }
    }

    $('#product_codes').change(function() {
        var product_id = $(this).val();

        $.ajax({
            url: 'controller/controller.inout.php?mode=getProductUnit',
            method: 'POST',
            data: {
                product_id:product_id
            },
            success:function(data) {
                var obj = $.parseJSON(data);

                $('#unit').val(obj.unit);

                $('#lotno').load('controller/controller.inout.php?mode=getLotnumber&product_id='+product_id);
                var lotno = $('#lotno option:selected').val();

                if(lotno != "") {
                    $('#qty_per_lot').val("");
                    $('#exp_date').val("");
                }

                if(obj.quantity == null){
                    $('#stock_quantity').val(0);
                } else {
                    $('#stock_quantity').val(obj.quantity);
                }
            }
        });
    })

    $('#lotno').change(function() {
        var stock_id = $(this).val();

        $.ajax({
            url: 'controller/controller.inout.php?mode=getExpirationDate',
            method: 'POST',
            data: {
                stock_id:stock_id
            },
            success:function(data) {
                var obj = $.parseJSON(data);

                $('#qty_per_lot').val(obj.log_qty);
                $('#transac_date').val(obj.transac_date);

                if(obj.exp_date == "0000-00-00") {
                    $('#exp_date').val("N/A");
                } else {
                    $('#exp_date').val(obj.exp_date);
                }

            }
        });
    })

</script>

<?php
require_once "./component/footer.php";