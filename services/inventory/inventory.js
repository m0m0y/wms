$(function(){
    
    $('#rak_id').load('controller/controller.inventory.php?mode=dropdown_rak');
    $('#maintenance-nav').toggleClass('active');

    productTable();
    stockmodal();
    stockForm();
    prepareLocation();

    changeView('#lot-set');
    
    $('#dataTableSearch').on('keyup', function(){
        $('#productTable').DataTable().search($(this).val()).draw();
    });
    $('#dataTableSearchdetails').on('keyup', function(){
        $('#productdetailsTable').DataTable().search($(this).val()).draw();
    });

        customFileInput();
        ajaxForm1();
    

})

function ajaxForm1(){
    $('.upload-form').on('submit', function(e){

        e.preventDefault();

        loader();

        var $inputs = $(this).find("input, select, button, textarea");
        var action = $(this).attr("action");
        var type = $(this).attr("method");
        // alert(action);
        var formData = new FormData(this);

        console.log("submitting form");
        $inputs.prop("disabled", true);

        $.ajax({
            url: action,
            type: type,
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) { 

                var data = JSON.parse(data);

                loader();
                
                $inputs.prop("disabled", false);
                $inputs.off('change');
                $inputs.val('');

                $.Toast(data.message, {
                    'width': 0,
                    'duration': 4000,
                    'position': 'top',
                    'align': 'right',
                    'zindex': 99999
                });
                $('#UploadModal').modal('hide');
            }
        })
        return false;
    })

}

function customFileInput(){
    $('.image-area').on('click', function(){
        /* trigger input */
        $('#'+$(this).data('target')).trigger('click');
        /* submit */
        $('#'+$(this).data('target')).on('change', function(){ 
            // alert("wow");
            $('.upload-form').submit();
            return
        })
    })
    return
}


function btn_upload(){
    $('#UploadModal').modal('show');
}

function deleteStock(stock_id,product_id){
    var data = [stock_id,product_id];
    confirmed(deleteStockCallback, "Do you really want to delete this order?", "Yes", "Cancel", data);
}

function deleteStockCallback(data){
    var stock_id = data[0];
    var product_id = data[1];
    $.ajax({
        url:"controller/controller.inventory.php?mode=deleteStock",
        method:"POST",
        data:{
            stock_id : stock_id,
            product_id: product_id
        },success:function(){
            $.Toast("Successfully Deleted", {
                'width': 0,
                'duration': 4000,
                'position': 'top',
                'align': 'right',
                'zindex': 99999
            });
            productdetailsTable(product_id);
        }
    });
    
}

function changeView(el){ $(el).fadeToggle(1); }

function prepareLocation(){
    $('#location_id').fadeOut();
    $('#location_type').on('change', function(){
        var type = $(this).val();
        $('#location_id').empty();
        switch(type) {
            case "rak":
                $('#location_id').load('controller/controller.inventory.php?mode=dropdown_rak');
                $('#location_id').fadeIn(); 
                break;
            default:
                $('#location_id').fadeOut(); 
        }
        return
    })


}


function viewproduct(product_id,product_code,product_name,uom,product_expiration,product_type){

    /* Hide Main Table and Show Detailed Table */

 
    if(product_type=="track" && product_expiration=="yes"){
        $('#stock_serialno').hide();
        $('#label_exp').show();
        $('#stock_expiration_date').show();
    }else if(product_type=="track" && product_expiration=="no"){
        $('#stock_serialno').hide();
        $('#label_exp').hide();
        $('#stock_expiration_date').hide();
    }else if(product_type=="serial"){
        $('#stock_serialno').show();
        $('#label_exp').hide();
        $('#stock_expiration_date').hide();
    }
    changeView('#product-set');
    changeView('#lot-set');
    $('#product-to-view').text(product_name);
    $('#uom').text(uom);

    productdetailsTable(product_id);

    $.ajax({
        url: "controller/controller.inventory.php?mode=analytics&product_id="+product_id,
        type: 'GET',
        processData: false,
        contentType: false,
        success: function(data) { 
            var data = JSON.parse(data);

            $('#analytic-rak')
                .css({ 'width' : data.rak_p + '%' })
                .html(data.rak_p + '%')
                .attr('title', data.rak + " on Rak")
                .tooltip();
            $('#l-or').html(data.rak);


            $('#analytic-cart')
                .css({ 'width' : data.cart_p + '%' })
                .html(data.cart_p + '%')
                .attr('title', data.cart + " on Cart")
                .tooltip();
            $('#l-it').html(data.cart);
            
            $('#analytic-truck')
                .css({ 'width' : data.trk_p + '%' })
                .html(data.trk_p + '%')
                .attr('title', data.trk + " on Truck")
                .tooltip();
            $('#l-ot').html(data.trk);

        }
    })

    $('#product_id').val(product_id);
    $('#product_code').val(product_code);

}

function stockmodal(){
    /* revert to add form on modal close */
    $('#StockModal').on('hide.bs.modal', function(){
        $('#stockForm').attr('action', 'controller/controller.inventory.php?mode=add');
        $('#stock_qty, #stock_lotno, #stock_serialno, #stock_expiration_date').val('');
        $('#location_type').prop('selectedIndex',0).trigger("change");
    })
}

function stockForm(){

    $('.Stock-form').on('submit', function(e){

        e.preventDefault();
        var $inputs = $(this).find("input, select, button, textarea");
        var action = $(this).attr("action");
        var type = $(this).attr("method");
        var formData = new FormData(this);
        
        $inputs.prop("disabled", true);
        $('.modal').modal('hide');

        $.ajax({
            url: action,
            type: type,
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) { 
                var data = JSON.parse(data);
                $.Toast(data.message, {
                    'width': 0,
                    'duration': 1000,
                    'position': 'top',
                    'align': 'right',
                    'zindex': 99999
                });

                productdetailsTable($('#product_id').val());
                $inputs.prop("disabled", false);
                $('#product_code').prop("disabled",true);
                $('#stock_qty').val("");
                $('#rak_id').val("");
                $('#stock_lotno').val("");
                $('#stock_serialno').val("");
                $('#stock_expiration_date').val("");
                $('#reference').val("");
                $('#notes').val("");
                $('#transaction_date').val("");
            }
        })
        return false;
    })
}

function editstock(stock_id,product_id,location_id,product_code,stock_lotno,stock_serialno,stock_qty,stock_expiration_date,log_reference,log_notes,log_transaction_date){
    $('#stock_id').val(stock_id);
    $('#product_id').val(product_id);
    $('#rak_id').val(location_id);
    $('#product_code').val(product_code);
    $('#stock_lotno').val(stock_lotno);
    $('#stock_serialno').val(stock_serialno);
    $('#stock_qty').val(stock_qty);
    $('#stock_expiration_date').val(stock_expiration_date);
    $('#reference').val(log_reference);
    $('#notes').val(log_notes);
    $('#transaction_date').val(log_transaction_date);
    $('#StockModal').modal('show');
    $('#stockForm').attr('action', 'controller/controller.inventory.php?mode=update');
}

function closedetails(){
    
    /* 
        var product_id = 0;
        productdetailsTable(product_id);
        $('#product_code').val("");
    */
    $('#rak_id').val("");
    $('#stock_lotno').val("");
    $('#stock_serialno').val("");
    $('#reference').val("");
    $('#notes').val("");
    $('#stock_expiration_date').val("");
    $('#transaction_date').val("");
    $('#product_id').val("");
    $('#stock_id').val("");
    $('#StockModal').modal('hide');
}
function printBarcode(stock_lotno){
    var product_code = $('#product_code').val();
    var lotno = product_code+stock_lotno;
    const url = "tcpdf/examples/lot.php?stock_lotno="+lotno;
    if(isElectron()) {
        embedpdf(url, '.main-content')
        return
    }
    window.open(url);
}

function printStockcard(stock_id){
    window.open("tcpdf/examples/stockcard.php?stock_id="+stock_id);
}


function productTable(){
    $('#productTable').DataTable().destroy();
    $('#productTable').DataTable({
        "bLengthChange": false,
        "pageLength": 5,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "ajax" : "controller/controller.inventory.php?mode=table",
        "columns" : [
            { "data" : "product_code"},
            { "data" : "product_description"},
            { "data" : "quantity"},
            { "data" : "uom"},
            { "data" : "action"}
        ]
    });

    $('#productTable').on( 'page.dt', function () {
        $('html, body').animate({
            scrollTop: 0
        }, 500);   
    });

    $.ajax({
        url: "controller/controller.inventory.php?mode=analytics&product_id=0",
        type: 'GET',
        processData: false,
        contentType: false,
        success: function(data) { 
            var data = JSON.parse(data);

            $('#m-analytic-rak')
                .css({ 'width' : data.rak_p + '%' })
                .html(data.rak_p + '%')
                .attr('title', data.rak + " on Rak")
                .tooltip();
            $('#a-or').html(data.rak);

            $('#m-analytic-cart')
                .css({ 'width' : data.cart_p + '%' })
                .html(data.cart_p + '%')
                .attr('title', data.cart + " on Cart")
                .tooltip();
            $('#a-it').html(data.cart);

            
            $('#m-analytic-truck')
                .css({ 'width' : data.trk_p + '%' })
                .html(data.trk_p + '%')
                .attr('title', data.trk + " on Truck")
                .tooltip();
            $('#a-ot').html(data.trk);

        }
    })
}

function productdetailsTable(product_id){
    $('#productdetailsTable').DataTable().destroy();
    $('#productdetailsTable').DataTable({
        "bLengthChange": false,
        "pageLength": 5,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "ajax" : "controller/controller.inventory.php?mode=tableDetails&product_id="+product_id,
        "columns" : [
            { "data" : "stock_lotno"},
            { "data" : "stock_serialno"},
            { "data" : "stock_qty"},
            { "data" : "uom"},
            { "data" : "location"},
            { "data" : "stock_expiration_date"},
            { "data" : "action"}
        ]
    });
}

function pushPin(el){
    var $cache = $(el);
    var vTop = $cache.offset().top - parseFloat($cache.css('margin-top').replace(/auto/, 0));
    var width = $cache.outerWidth();
    $(window).scroll(function (event) {
        var y = $(this).scrollTop();

        if (y >= vTop) {
            $cache.addClass('stuck');
            $cache.css({
                'width': width
            })
        } else {
            $cache.removeClass('stuck');
            $cache.css({
                'width': '100%'
            })
        }
    });
}