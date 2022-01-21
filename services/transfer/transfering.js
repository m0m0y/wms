$(document).scannerDetection({
	   
    timeBeforeScanTest: 200,
    avgTimeByChar: 40,
    // preventDefault: true,
  
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
    transferRequest();
    ajaxForm();
    $('body').on('click', '.card-with-lot.ispicked', function(){
        $('#undo-modal').modal('show');    
    });
    $('#btn_undoSave').on('click',function(){
        var $el = $('.card-with-lot.ispicked');
        undoSave($el);
    });
});

/* hanlde scan detection */

function prepareScan(string) {
    /* capture scan if transfer modal is shown */
    if(($("#transfer_modal").data('bs.modal') || {})._isShown){ getLocation(string); return }
    if(($("#transfer_form").data('bs.modal') || {})._isShown){ return }
    validateScanned(string); 
    return;
}

function transferRequest() {
    /* open request modal */
    $('#btn_transfer').on('click', function(){
        $('#transfer_modal').modal('show');
    })
    // $.ajax({
    //     url:"controller/controller.transfering.php?mode=getlocations",
    //     method:"GET",
    //     success:function(data){
    //         let response = JSON.parse(data);
    //         $('#location-id').html(response.message);
    //         return      
    //     }
    // });
}

function getLocation(lot) {
    
    $.ajax({
        url:"controller/controller.transfering.php?mode=getmovable&i="+lot,
        method:"GET",
        success:function(data){
            let response = JSON.parse(data);
            if(response.code == 0){
                $.Toast(response.message, {
                    'duration': 4000,
                    'position': 'top',
                    'align': 'right',
                    'class': 'bg-danger'
                });
                return
            }
            $('#stock-count').hide();
            $('#stock-id').html(response.message);
            $('#stock-id').prepend('<option selected disabled>Select Location</option>')
            $('#transfer_modal').modal('hide');
            $('#transfer_form').modal('show');
            transferForm();
            return      
        }
    });
}

function transferForm(){
    $('#stock-id').on('change', function(){
        var stock = $(this).find(':selected').attr('data-stock');
        $('#qty-stock').attr('max', stock).val(stock);    
        $('#stock-count').fadeIn('500');

        var stockid = $('#stock-id').val();

        $.ajax({
            url:"controller/controller.transfering.php?mode=getlocations",
            method:"POST",
            data:{
                stockid: stockid
            },
            success:function(data){
                let response = JSON.parse(data);
                $('#location-id').html(response.message);
                return      
            }
        });


    })
}

function ajaxForm(){

    $('.ajax-form').on('submit', function(e){

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
                $inputs.prop("disabled", false);
            }
        })
        return false;
    })
}

function pickOrder(id, rak_id, stock_id, message = '') {
    
    loader();
    window.history.replaceState(null, null, window.location.pathname);
    $('#viewcache_id').val(id)
    $('#viewcache_name').val(rak_id)
    $('#viewcache_no').val(stock_id)

    $.ajax({
        url: "controller/controller.transfering.php?mode=transfering&i="+id+"&m="+rak_id+"&n="+stock_id,
        method: "GET",
        success: function(data){
            
            var datum = JSON.parse(data);
            if(datum.code == '0') { 
                $.Toast(datum.message, {
                    'width': 0,
                    'duration': 4000,
                    'position': 'top',
                    'align': 'left',
                    'zindex': 99999
                });
                return 
            }
            
            if(message !== ""){ $.Toast(message, {'position':'top','align':'right', 'class':'bg-success'}); }
            
            $('.user-picking')
                .empty()
                .html(datum.view)
                .toggleClass('active');

            validateLot();

            setTimeout(function(){
                loader();
            }, 1000)
        }
    })   
    return
}



function hardReload(e = '&c=false'){
    
    var i = $('#viewcache_id').val();
    var n = $('#viewcache_name').val();
    var m = $('#viewcache_no').val();

    var url = 'http://' + window.location.hostname + window.location.pathname;

    window.location.href = url+'?i='+i+'&n='+n+'&m='+m+e;
    return
}

function audioTrigger(e){
    $(e)[0].play();
    return
}

function validateScanned(pasted){

    if(!$('#user-picking').hasClass('active')) { return }
        
    var $el = $('.card-with-lot:focus');
    var lot = $el.data('product_code')+$el.data('lot');
    var reallot = $el.data('lot');
    if($('#pickingQuantity').val() != ""){
        $('#pickingRak').val(pasted).trigger('change');
        return; 
    } 

    if($el.hasClass("ispicked")) { /* undoPick($el); */ return }

    if(typeof lot === "undefined") { audioTrigger('#audio_incorrect'); $.Toast("Select lot number first", {'position':'top','align':'right', 'class' : 'bg-danger'}); return; }
    if(pasted === "") { audioTrigger('#audio_incorrect'); $.Toast("Invalid lot number", {'position':'top','align':'right', 'class' : 'bg-danger'}); return; }
    
    if(lot.toString().toLowerCase() === pasted.toString().toLowerCase()){
        audioTrigger('#audio_correct');
        $('#validity-fail').modal('hide');
        $('#validity-modal').modal('show');
        $('#order_details_id').val( $el.data('id'));
        $('#stock_id').val( $el.data('stockid'));
        $('#product_id').val( $el.data('productid'));
        $('#stock_lotno').val(reallot);
        $('#stock_serialno').val( $el.data('serial'));
        $('#stock_qty').val( $el.data('qty'));
        $('#stock_expiration_date').val( $el.data('expire'));
        $('#pickingQuantity').attr('placeholder', $el.data('remaining')).val($el.data('remaining'));
        $('#moving_to_rak').val($el.data('rak_id'));
    
    } else {
        audioTrigger('#audio_incorrect');
        $('#validity-fail').modal('show');
        resetForm();
    }  
}

function validateLot() {
    $('#validity-modal, #validity-fail, #validity-cart').on('hidden.bs.modal', function(){
        resetForm();
    });
}
function resetForm(){
    $('#pickingRak, #pickingQuantity, #order_details_id, #stock_id, #product_id, #stock_lotno, #stock_serialno, #stock_qty, #stock_expiration_date').val('');
    $('#choose-cart').fadeOut('500', function(){
        $('#choose-qty').fadeIn('500')    
    })
    return
}

function chooseRak()
{

    $('#choose-qty').fadeOut('500', function(){
        $('#pickingRak').val('')
        $('#choose-cart').fadeIn('500')    
    })
    
    
    $('#pickingRak').off('change'); /* prevent multiple validation */
    $('#pickingRak').on('change', function(){
        var location = $(this).val();
        var moving_to_rak =$('#moving_to_rak').val();

        if(moving_to_rak==0 ||moving_to_rak=='0'){

            validateRak(location);
            return

        }else{

            if(location!=moving_to_rak){

                $('#pickingRak').val('');
                audioTrigger('#audio_incorrect'); $.Toast("Rak not matched", {'position':'top','align':'right', 'class' : 'bg-danger'});
                return

            }else{
                savePick();
                return
            } 

        }

        

    })
}

function validateRak(location){
    
    $.ajax({
        url:"controller/controller.transfering.php?mode=validateRak",
        method:"POST",
        data:{
            location: location
        },success:function(data){
            var b = $.parseJSON(data);
            if(b.code==0){

                $('#pickingRak').val('');
                audioTrigger('#audio_incorrect'); $.Toast("Invalid Rak", {'position':'top','align':'right', 'class' : 'bg-danger'});
                return

            }else{

                savePick();
                return
            }
        }
    });

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
    var pickingRak = $('#pickingRak').val();
    var transfer_id = $('#viewcache_id').val();

    if($.trim(pickingRak) == null || $.trim(pickingRak) == "")
    {
        $.Toast("Cart Validation Failed", {
            'duration': 4000,
            'position': 'top',
            'align': 'left',
        });
        return
    }
    
    loader();

    $.ajax({
        url:"controller/controller.transfering.php?mode=add",
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
            pickingRak: pickingRak,
            transfer_id: transfer_id
        },success:function(){
            hardReload('&c=item picked');
            return      
        }
    });
}


function undoSave($el){

    $.ajax({
        url:"controller/controller.transfering.php?mode=undo",
        method:"POST",
        data:{
            id: $el.data('id'),
            stock_id: $el.data('stockid'),
            stock_lotno: $el.data('lot'),
            stock_expiration_date: $el.data('expire'),
            stock_qty: $el.data('qty'),
            from_stock_id: $el.data('from_stock_id'),
            rak_return_id: $el.data('stock_id'),
            productid: $el.data('productid'),
            serial: $el.data('serial'),
            transfer_id: $el.data('transfer_id')
        },

        success:function(data){
            var b = $.parseJSON(data);
            if(b.stat=="invalid"){
                alert("Undo failed. Invalid Rak");
            } else {
                hardReload('&c=undo success');
                return
            }
        }
    });
}

function toTable(transfer_id){
    confirmed(toTableCallback, "End Transaction ?", "Yes", "Maybe Later", transfer_id);
}

function toTableCallback(transfer_id){
    $.ajax({
        url:"controller/controller.transfering.php?mode=endtransaction",
        method:"POST",
        data:{ transfer_id : transfer_id },
        success:function(){
            window.location.href="transfering.php";
        }
    });
    return
}