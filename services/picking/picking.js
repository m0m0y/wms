var errorToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-danger"}
var successToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-primary"}

$(document).scannerDetection({
	   
    timeBeforeScanTest: 200,
    avgTimeByChar: 40,
    endChar: [13],
    onComplete: function(barcode, qty){
        validScan = true;
        prepareScan(barcode);
    },
    onError: function(string, qty) {
        prepareScan(string);
    }
});

$(function(){

    /* delegated event */
    $('body').on('click', '.card-with-lot.ispicked', function(){
        $('#undo-modal').attr("data-card", $(this).data('id'));

        // $('#undo_pickingQuantity').val($(this).data('qty'));
        $('#undo_pickingQuantity')
            .attr('placeholder', $(this).data('qty'))
            //.val($(this).data('qty'))
            .attr("max", $(this).data('qty'))
            .attr("min", 1);
        $('#stock_id').val($(this).data('stockid'));
        $('#undo-modal').modal('show');
        validateInput('#undo_pickingQuantity');
    });

    showLot();
    generateUserSelect();

    RefreshPageforNewOrder();

});

var timeout = 0;
function minusDown() {
  timeout = setInterval(function(){ 
    var pickingQuantity = $('#pickingQuantity').val();
    pickingQuantity = parseInt(pickingQuantity) - 1;
    if(pickingQuantity <=0){
        pickingQuantity = 1;
    }
    $('#pickingQuantity').val(pickingQuantity);
  }, 100);
}

function minusUp() {
  clearInterval(timeout);
}

function addDown() {
  timeout = setInterval(function(){ 
    var maxnum = $('#pickingQuantity').attr("max");
    var pickingQuantity = $('#pickingQuantity').val();
    pickingQuantity = parseInt(pickingQuantity) + 1;
    if(pickingQuantity > maxnum){
        pickingQuantity = maxnum;
    }
    $('#pickingQuantity').val(pickingQuantity);
  }, 100);
}

function addUp() {
  clearInterval(timeout);
}

function RefreshPageforNewOrder(){
    setInterval(function(){ 
        $("#live").load(location.href + " #live>*", "");
    }, 5000);
}

function prepareScan(barcode){
    /* if($("input:focus")) { alert("tite"); return } */

    if($('#pickingQuantity, #undo_pickingQuantity').is(':focus')) { return }

    if(($("#table-modal").data('bs.modal') || {})._isShown){ validateTable(barcode); return }
    if(($("#undo-modal").data('bs.modal') || {})._isShown){ undoSave(barcode); return }
    if(!($("#manual-modal").data('bs.modal') || {})._isShown){
        validateScanned(barcode);
    }
    return
} 

function validateLot() {
    $('#validity-modal, #validity-fail, #validity-cart').on('hidden.bs.modal', function(){
        resetForm();
    });
}

function manualInput(){
    var $el = $('.manual_btn:focus');

    $('.submit_btn').attr("data-id",$el.data('id'));
    $('.submit_btn').attr("data-from_stock_id",$el.data('from_stock_id'));
    $('.submit_btn').attr("data-stockid",$el.data('stockid'));
    $('.submit_btn').attr("data-productid",$el.data('productid'));
    $('.submit_btn').attr("data-product_code",$el.data('product_code'));
    $('.submit_btn').attr("data-lot",$el.data('lot'));
    $('.submit_btn').attr("data-serial",$el.data('serial'));
    $('.submit_btn').attr("data-qty",$el.data('qty'));
    $('.submit_btn').attr("data-order",$el.data('order'));
    $('.submit_btn').attr("data-remaining",$el.data('remaining'));
    $('.submit_btn').attr("data-expire",$el.data('expire'));
    $('.submit_btn').attr("data-location_id",$el.data('location_id'));

    $('#manual-modal').modal('show');
    $('#lotnumber_manual').val("");
    
}

function submitManual(){
    var $el = $('.submit_btn:focus');
    var lot = $el.data('product_code')+$el.data('lot');
    var reallot = $el.data('lot');

    var lotnumber_manual = $('#lotnumber_manual').val();
    if(lotnumber_manual===""){  audioTrigger('#audio_incorrect'); $.Toast("Invalid lot number", errorToast); return; }
    if(lotnumber_manual === lot){

        audioTrigger('#audio_correct');
        $('#validity-modal').modal('show');
        $('#order_details_id').val( $el.data('id'));
        $('#stock_id').val( $el.data('stockid'));
        $('#product_id').val( $el.data('productid'));
        $('#stock_lotno').val(reallot);
        $('#stock_serialno').val( $el.data('serial'));
        $('#stock_qty').val( $el.data('qty'));
        $('#stock_expiration_date').val( $el.data('expire'));
        $('#location_id').val($el.data('location_id'));
        $('#pickingQuantity').attr('placeholder', $el.data('remaining')).val($el.data('remaining')).attr("max", $el.data('remaining')).attr("min", 1);
        $('#manual-modal').modal('hide');

        validateInput('#pickingQuantity');
    }
    else
    {

        audioTrigger('#audio_incorrect');
        $('#validity-fail').modal('show');
        resetForm();
        $('#manual-modal').modal('hide');

    }
}

function validateScanned(pasted){
        
        var $el = $('.card-with-lot:focus');
        var lotnumber = $el.data('product_code')+$el.data('lot');
        var lot = $el.data('lot');
        var reallot = $el.data('lot');
        if($('#pickingQuantity').val().trim() != ""){
            $('#pickingCart').val(pasted).trigger('change');
            return; 
        }

        if($el.hasClass("ispicked")) { undoPick($el); return }
        if(typeof lot === "undefined") { audioTrigger('#audio_incorrect'); $.Toast("Select lot number first", errorToast); return; }
        if(pasted === "") { audioTrigger('#audio_incorrect'); $.Toast("Invalid lot number", errorToast); return; }
        
        if(lotnumber.toString().toLowerCase() === pasted.toString().toLowerCase()){
            audioTrigger('#audio_correct');
            $('#validity-modal').modal('show');
            $('#order_details_id').val( $el.data('id'));
            $('#stock_id').val( $el.data('stockid'));
            $('#product_id').val( $el.data('productid'));
            $('#stock_lotno').val(reallot);
            $('#stock_serialno').val( $el.data('serial'));
            $('#stock_qty').val( $el.data('qty'));
            $('#stock_expiration_date').val( $el.data('expire'));
            $('#location_id').val($el.data('location_id'));
            $('#pickingQuantity')
                .attr('placeholder', $el.data('remaining'))
                .val($el.data('remaining'))
                .attr("max", $el.data('remaining'))
                .attr("min", 1);
            
            validateInput('#pickingQuantity');
    
        } else {
            audioTrigger('#audio_incorrect');
            $('#validity-fail').modal('show');
            resetForm();
        }  
}

function resetForm(){
    $('#pickingCart, #pickingQuantity, #order_details_id, #stock_id, #product_id, #stock_lotno, #stock_serialno, #stock_qty, #stock_expiration_date').val('');
    $('#choose-cart').fadeOut('500', function(){
        $('#choose-qty').fadeIn('500') 
        $('#selection').fadeOut('100') 
    })
    return
}


function updateUser(slip_id){
    $('#users_modal').modal('show');
    var userid = "#user_id"+slip_id;
    var user_id = $(userid).val();
    $('#users').val(user_id);
    $('#slip_idforpicker').val(slip_id);
}

function deleteOrder(slip_id){
    confirmed(deleteOrderCallback, "Do you really want to delete this order?", "Yes", "Cancel", slip_id);
}

function deleteOrderCallback(slip_id){
    $.ajax({
        url:"controller/controller.picking.php?mode=deleteOrder",
        method:"POST",
        data:{
            slip_id:slip_id
        },success:function(){
            $.Toast("Successfully Deleted", {
                'duration': 4000,
                'position': 'top',
                'align': 'right',
            });
        }
    })
}

function savenewUser(){
    $('#users_modal').modal('hide');
    confirmed(savenewUserCallback, "Do you really want to change the picker for this order?", "Yes", "Cancel");

}

function savenewUserCallback(){

    var users = $("#users").val();
    var slip_idforpicker = $("#slip_idforpicker").val();

    $.ajax({
        url:"controller/controller.picking.php?mode=savenewUser",
        method:"POST",
        data:{
            users : users,
            slip_idforpicker : slip_idforpicker
        },success:function(){
            window.location.href="picking.php";
        }
    });

}


function generateUserSelect(){
    $.ajax({
        url: "controller/controller.picking.php?mode=usersOption",
        type: 'GET',
        processData: false,
        contentType: false,
        success: function(data) { 
            var data = JSON.parse(data)
            $('#users').append(data.html);
        }
    })
}

function pickOrder(id, name, no, c = '') {
    
    loader();
    window.history.replaceState(null, null, window.location.pathname);
    $('#viewcache_id').val(id)
    $('#viewcache_name').val(name)
    $('#viewcache_no').val(no)

    $.ajax({
        url: "controller/controller.picking.php?mode=picking&i="+id+"&m="+name+"&n="+no,
        method: "GET",
        success: function(data){
            var datum = JSON.parse(data);
            if(datum.code == '0') { 
                $.Toast(datum.message, errorToast);
                return 
            }

            if(c !== ""){ $.Toast(c, successToast); }

            $('#side-icon').html("keyboard_backspace");
            $('.sidebar-trigger').off('click');
            $('.sidebar-trigger').on('click', function(){
                window.location.reload();
            });

            $('.user-picking')
                .empty()
                .html(datum.view)
                .toggleClass('active');
                
            /*  sort result by relevance */

            var pick = $(".pick");
            pick.sort(function(a, b){
                return $(a).data("id")-$(b).data("id")
            });
            $("#picks").html(pick);

            validateLot();
            setTimeout(function(){
                loader();
            }, 1000)
        }

    })   
    return
}

function undoB(){

    if($('#undo_pickingQuantity').val() == "") {
        $.Toast("Please Input Quantity", {
            'duration': 4000,
            'position': 'top',
            'align': 'right', 
            errorToast
        });
    } else {
        $('#undoA').fadeOut(500);
        $('#undoC').fadeIn(500);

        var undo_pick_val = $('#undo_pickingQuantity').val();

        $('#undo_pick').val(undo_pick_val);
    }
}

function scan() {
    $('#undoC').fadeOut(500);
    $('#undoB').fadeIn(500);
}

function manual() {
    var moy = $('#undo_pick').val();
    $('#rak-manual-modal').modal('show');

    $('#undo_pickQuan').val(moy);
    $('#rak_id').load('controller/controller.picking.php?mode=dropdown_rak');
}

function undoSaveManual(){
    var current = $('#undo-modal').attr('data-card');
    var $el = $('.card-with-lot.ispicked[data-id="' + current +'"]');
    var stock_id = $('#stock_id').val();
    var undo_pickingQuantity = $('#undo_pickQuan').val();
    var rak_return_id = $('#rak_id').val();

    if(rak_return_id === null) {
        $.Toast("Invalid rak", errorToast);
    } else {
        $.ajax({
            url:"controller/controller.picking.php?mode=undo",
            method:"POST",
            data:{
                id: $el.data('id'),
                stock_id: stock_id,
                stock_lotno: $el.data('lot'),
                stock_expiration_date: $el.data('expire'),
                stock_qty: $el.data('qty'),
                rak_return_id: rak_return_id,
                productid: $el.data('productid'),
                serial: $el.data('serial'),
                undo_qty: undo_pickingQuantity
            },
            success:function(data){
                var b = JSON.parse(data);
                if(b.stat=="invalid"){
                    $.Toast("Invalid rak", errorToast);
                    return
                }
                hardReload('&c=undo success');
                return
                
            }
        });
    }
}

function undoSave(rak_return_id){

    var current = $('#undo-modal').attr('data-card');
    var $el = $('.card-with-lot.ispicked[data-id="' + current +'"]');
    var stock_id = $('#stock_id').val();
    var undo_pickingQuantity = $('#undo_pickingQuantity').val();


    $.ajax({
        url:"controller/controller.picking.php?mode=undo",
        method:"POST",
        data:{
            id: $el.data('id'),
            stock_id: stock_id,
            stock_lotno: $el.data('lot'),
            stock_expiration_date: $el.data('expire'),
            stock_qty: $el.data('qty'),
            // from_stock_id: $el.data('from_stock_id')
            rak_return_id: rak_return_id,
            productid: $el.data('productid'),
            serial: $el.data('serial'),
            undo_qty: undo_pickingQuantity


        },
        success:function(data){
            var b = JSON.parse(data);
            if(b.stat=="invalid"){
                $.Toast("Invalid rak", errorToast);
                return
            }
            hardReload('&c=undo success');
            return
            
        }
    });

}


function chooseCart()
{

    var pickingQuantity = $('#pickingQuantity').val();
    var stock_qty = $('#stock_qty').val();

    if(parseFloat(pickingQuantity) > parseFloat(stock_qty)){
            $.Toast("Insufficient Stock", {
                'duration': 4000,
                'position': 'top',
                'align': 'right',
            });
    }else if(parseFloat(pickingQuantity) <= 0){
            $.Toast("Invalid Quantity", {
                'duration': 4000,
                'position': 'top',
                'align': 'right',
            });
    }else{
            $('#choose-qty').fadeOut('500', function(){
                $('#selection').fadeIn('500');
            })
            
            
            $('#pickingCart').off('change'); /* prevent multiple validation */
            $('#pickingCart').on('change', function(){
                var location = $(this).val();
                $.ajax({
                    url:"controller/controller.picking.php?mode=validateStorage&i="+location+'&type=Cart',
                    method:"GET",
                    success:function(data){
                        
                        let response = JSON.parse(data);
                        if(response.code == 0){
                            $('#pickingCart').val('');
                            $.Toast(response.message, {
                                'duration': 4000,
                                'position': 'top',
                                'align': 'left',
                            });
                            return
                        }
                        savePick();
                        return      
                    }
                });
            })
    }

    
}

function scan_pick() {
    $('#selection').fadeOut(500);
    $('#choose-cart').fadeIn('500');
}

function manual_pick(){
    $('#cart-manual-modal').modal('show');
    $('#cart_id').load('controller/controller.picking.php?mode=dropdown_cart');
}

function cartPick(){
    var location = $('#cart_id').val();

    $.ajax({
        url:"controller/controller.picking.php?mode=validateStorage&i="+location+'&type=Cart',
        method:"GET",
        success:function(data){
            
            let response = JSON.parse(data);
            if(response.code == 0){
                $('#cart_id').val('');
                $.Toast(response.message, {
                    'duration': 4000,
                    'position': 'top',
                    'align': 'left',
                });
                return
            }
            savePickManual();
            return      
        }
    });
}

function savePickManual() {
    var order_details_id = $('#order_details_id').val();
    var stock_id = $('#stock_id').val();
    var product_id = $('#product_id').val();
    var stock_lotno = $('#stock_lotno').val();
    var stock_serialno = $('#stock_serialno').val();
    var stock_qty = $('#stock_qty').val();
    var stock_expiration_date = $('#stock_expiration_date').val();
    var pickingQuantity = $('#pickingQuantity').val();
    var pickingCart = $('#cart_id').val();
    var slip_id = $('#viewcache_id').val();
    var location_id = $('#location_id').val();
    // alert(cart_id);
    if($.trim(pickingCart) == null || $.trim(pickingCart) == "")
    {
        $.Toast("Cart Validation Failed", {
            'duration': 4000,
            'position': 'top',
            'align': 'right',
        });
        return
    }
    
    loader();

    $.ajax({
        url:"controller/controller.picking.php?mode=add",
        method:"POST",
        data:{
            order_details_id: order_details_id,
            stock_id: stock_id,
            product_id: product_id,
            stock_lotno: stock_lotno,
            stock_serialno: stock_serialno,
            stock_qty: stock_qty,
            stock_expiration_date: stock_expiration_date,
            pickingQuantity: pickingQuantity,
            pickingCart: pickingCart,
            slip_id: slip_id,
            location_id: location_id
        },
        success:function(){
            hardReload('&c=item picked');
            return      
        }
    });
}

function validateTable(tableid){
    $.ajax({
        url:"controller/controller.picking.php?mode=validateStorage&i="+tableid+'&type=Table',
        method:"GET",
        success:function(data){
            let response = JSON.parse(data);
            if(response.code == 0){
                $.Toast(response.message, {
                    'duration': 4000,
                    'position': 'top',
                    'align': 'right',
                });
                return
            }

            toInvoice(tableid);
            return      
        }
    });
}

function toTable(e){
    $('#choices').fadeIn(500);
    $('#scan-table').fadeOut(100);
    $('#toTable').attr('data-target', e);
    return
}

function scanTable(){
    $('#scan-table').fadeIn(500);
    $('#choices').fadeOut(500);
}

function manualInputTable(){
    $('#table-manual-modal').modal('show');
    $('#table_id').load('controller/controller.picking.php?mode=dropdown_table');
}

function tablePick(){
    var table_id = $('#table_id').val();
    $.ajax({
        url:"controller/controller.picking.php?mode=validateStorage&i="+table_id+'&type=Table',
        method:"GET",
        success:function(data){
            let response = JSON.parse(data);
            if(response.code == 0){
                $.Toast(response.message, {
                    'duration': 4000,
                    'position': 'top',
                    'align': 'right',
                });
                return
            }

            toInvoice(table_id);
            return      
        }
    });
}

function toInvoice(barcode){
    var stockid = $('#toTable').data('target');
    $.ajax({
        url:"controller/controller.picking.php?mode=invoice&id="+stockid+"&table="+barcode,
        method:"GET",
        success:function(data){
            //dinagdagan ko ng port
            var url = 'http://' + window.location.hostname + window.location.pathname;
            // var url = 'http://' + window.location.hostname + window.location.pathname;
            window.location.href = url+'?t=picking was sent to invoice';
        }
    });
    // alert(stockid);
}

function savePick(){

    var order_details_id = $('#order_details_id').val();
    var stock_id = $('#stock_id').val();
    var product_id = $('#product_id').val();
    var stock_lotno = $('#stock_lotno').val();
    var stock_serialno = $('#stock_serialno').val();
    var stock_qty = $('#stock_qty').val();
    var stock_expiration_date = $('#stock_expiration_date').val();
    var pickingQuantity = $('#pickingQuantity').val();
    var pickingCart = $('#pickingCart').val();
    var slip_id = $('#viewcache_id').val();
    var location_id = $('#location_id').val();
    // alert("sdsd");
    if($.trim(pickingCart) == null || $.trim(pickingCart) == "")
    {
        $.Toast("Cart Validation Failed", {
            'duration': 4000,
            'position': 'top',
            'align': 'right',
        });
        return
    }
    
    loader();

    $.ajax({
        url:"controller/controller.picking.php?mode=add",
        method:"POST",
        data:{
            order_details_id: order_details_id,
            stock_id: stock_id,
            product_id: product_id,
            stock_lotno: stock_lotno,
            stock_serialno: stock_serialno,
            stock_qty: stock_qty,
            stock_expiration_date: stock_expiration_date,
            pickingQuantity: pickingQuantity,
            pickingCart: pickingCart,
            slip_id: slip_id,
            location_id: location_id
        },
        success:function(){
            hardReload('&c=item picked');
            return      
        }
    });
}

function hardReload(e = '&c=false'){
    
    var i = $('#viewcache_id').val();
    var n = $('#viewcache_name').val();
    var m = $('#viewcache_no').val();
    //dinagdagan ko ng port
    var url = 'http://' + window.location.hostname + window.location.pathname;
    // var url = 'http://' + window.location.hostname + window.location.pathname;

    window.location.href = url+'?i='+i+'&n='+n+'&m='+m+e;
    return
}

function audioTrigger(e){
    $(e)[0].play();
    return
}

function showLot(){
    $('body').on('click', '.pick-main', function(){
        $(this).find(".to-pick-count").focus();
        $("." + $(this).data('target')).each(function(){
            $(this).toggleClass("active");
        });
    })
    $('#undo-modal').on('hidden.bs.modal', function (e) {
        $('#undo_pickingQuantity').val('');
        $('#undoA').fadeIn(500);
        $('#undoB').fadeOut(500);
        $('#undoC').fadeOut(500);
    })
}


/* input validation */

function validateInput (selector) {
 
    $(selector).on('input, blur, keyup', function () {
        const val = $(this).val()
        const max = $(this).attr('max')
        const min = $(this).attr('min')
        if (val > max) { 
            $.Toast("You are picking items more than what you need!", {
                'duration': 4000,
                'position': 'top',
                'align': 'right',
            });
            $(this).val(max)
        }
        if (val < min) { 
            $(this).val('')
            $.Toast("Atleast 1 value is required for you to pick this item.", {
                'duration': 4000,
                'position': 'top',
                'align': 'right',
            });
        }
    });
}