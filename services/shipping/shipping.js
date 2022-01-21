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
    $('body').on('click', '.card_box.jerico', function(){
        $('#undo-modal').attr("data-card", $(this).data('box_number'));
        $('#undo-modal').modal('show'); 
    });

    showLot();

    RefreshPageforNewOrder();

    $('#btn_deliver').on('click', function(){

        confirmed(toDeliverCallback, "Mark transaction as Shipped ?", "Yes", "No");
        $('#delivery_modal').modal('hide');
    });

    generateDispatcherSelect();

});

function movetoDeliver(){
    var box_numbers = [];
    $.each($("input[name='box_number_cbox']:checked"), function(){            
        box_numbers.push($(this).val());
    });
    if(box_numbers=="" || box_numbers==null){
        $.Toast("Please select box to continue.", errorToast);
    }else{
        $('#validity-modal').modal('show');
    }
    
}
function generateDispatcherSelect(){
    
    $.ajax({
        url: "controller/controller.shipping.php?mode=option",
        type: 'GET',
        processData: false,
        contentType: false,
        success: function(data) { 
            var data = JSON.parse(data)
            $("select[name='assign_to']").empty();
            $("select[name='assign_to']").append("<option value='0' selected='' disabled>Select Dispatcher</option>");
            $('#assign_to').append(data.html);
        }
    })
}

function RefreshPageforNewOrder(){
    setInterval(function(){ 
        $("#live").load(location.href + " #live>*", "");
    }, 5000);
}

function repack(slip_id){
    confirmed(repackCallback, "Send this<br>transaction back to packer?", "Repack", "Cancel", slip_id);
}

function repackCallback(slip_id){
    $.ajax({
        url:"controller/controller.shipping.php?mode=repack",
        method:"POST",
        data:{
            slip_id:slip_id
        },success:function(){
            alert("Successfully saved");
            window.location.href="shipping.php";
        }
    })
}

function prepareScan(barcode){

    if(($("#undo-modal").data('bs.modal') || {})._isShown){ undoSave(barcode); return }
    validateScanned(barcode);
    return
}

function validateBox() {
    $('#validity-modal, #validity-fail, #validity-cart').on('hidden.bs.modal', function(){
        resetForm();
    });
}

function validateScanned(pasted){
        
        var $el = $('.card_box:focus');
        // var box_number = $el.data('box_number');
        var box_number = "";
        var boxx = $('#boxx').val();
        if($('#pickingQuantity').val() != ""){
            $('#pickingCart').val(pasted).trigger('change');
            return; 
        } 
        if($el.hasClass("ispicked")) { undoPick($el); return }
        // if(typeof box_number === "undefined") {
            if($('#delivery_modal').is(':visible')){

            }else{
                var bc = boxx.split(",");
                var count = bc.length;
                var a = 0;
                var b = 0;
                var correct_box = "";
                for(a==0;a<count;a++){
                    var current_box = bc[a];
                    if(current_box.toString().toLowerCase() === pasted.toString().toLowerCase()){
                        b += 1;   
                        audioTrigger('#audio_correct');
                        $('#box_number').val(current_box);
                        $('#validity-modal').modal('show');
                    }
                }
                if(b==0){
                        audioTrigger('#audio_incorrect');
                        $('#validity-fail').modal('show');
                        resetForm();
                }
                
                // audioTrigger('#audio_incorrect'); $.Toast("Select box number first", errorToast); return; 
                // alert("s");
            }
         
        // }
        if(pasted === "") { audioTrigger('#audio_incorrect'); $.Toast("Invalid Box number", errorToast); return; }
        
        // if(box_number.toString().toLowerCase() === pasted.toString().toLowerCase())
        // {
        //     audioTrigger('#audio_correct');
        //     $('#box_number').val(box_number);
        //     $('#validity-modal').modal('show');
        // }
        // else 
        // {
        //     audioTrigger('#audio_incorrect');
        //     $('#validity-fail').modal('show');
        //     resetForm();
        // }  
}

function resetForm(){
    
    $('#pickingQuantity').val('');
    $('#choose-cart').fadeOut('500', function(){
        $('#choose-qty').fadeIn('500')    
    })
    return
}

function pickOrder(id, name, no, c = '') {
    
    loader();
    window.history.replaceState(null, null, window.location.pathname);
    $('#viewcache_id').val(id)
    $('#viewcache_name').val(name)
    $('#viewcache_no').val(no)

    $.ajax({
        url: "controller/controller.shipping.php?mode=shipping&i="+id+"&m="+name+"&n="+no,
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

            validateBox();
            setTimeout(function(){
                loader();
            }, 1000)
        }

    })   
    return
}

function undoSave(rak_return_id){

    var current = $('#undo-modal').attr('data-card');
    var $el = $('.card_box.jerico[data-box_number="' + current +'"]');
    $.ajax({
        url:"controller/controller.shipping.php?mode=undo",
        method:"POST",
        data:{
            box_number: $el.data('box_number'),
            rak_return_id: rak_return_id
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

    $('#choose-qty').fadeOut('500', function(){
        $('#pickingCart').val('')
        $('#choose-cart').fadeIn('500')
        $('#pickingQuantity').val('1')    
    })
    
    
    $('#pickingCart').off('change'); /* prevent multiple validation */
    $('#pickingCart').on('change', function(){
        var location = $(this).val();
        $.ajax({
            url:"controller/controller.shipping.php?mode=validateStorage&i="+location+'&type=Truck',
            method:"GET",
            success:function(data){
                
                let response = JSON.parse(data);
                if(response.code == 0){
                    $('#pickingCart').val('')
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

function toDeliver(slip_id){
    
    // confirmed(toDeliverCallback, "Mark transaction as Shipped ?", "Yes", "No", slip_id);
    $('#deliver_slip_id').val(slip_id);
    $('#delivery_modal').modal('show');

}

function toDeliverCallback(){
    var slip_id = $('#deliver_slip_id').val();
    var do_number = $('#do_number').val();
    var assign_to = $('#assign_to').val();

    $.ajax({
        url:"controller/controller.shipping.php?mode=deliver",
        method:"POST",
        data:{ 
            slip_id : slip_id,
            do_number : do_number,
            assign_to : assign_to
         },
        success:function(){
            window.location.href="shipping.php";
        }

    });
    return
}

function savePick(){

    var pickingCart = $('#pickingCart').val();
    var box_number = $('#box_number').val();
    // var box_numbers = [];
    // $.each($("input[name='box_number_cbox']:checked"), function(){            
    //     box_numbers.push($(this).val());
    // });
    // box_numbers = box_numbers.toString();

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
        url:"controller/controller.shipping.php?mode=add",
        method:"POST",
        data:{
            // box_number: box_numbers,
            box_number: box_number,
            pickingCart: pickingCart
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
}
