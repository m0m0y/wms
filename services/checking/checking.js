var toasts = { 'duration': 4000,'position': 'top','align': 'right','class' : 'bg-danger','zindex': 99999 };
var toasts_correct = { 'duration': 4000,'position': 'top','align': 'right','class' : 'bg-primary','zindex': 99999 };

$(function(){
    approveModal();
    manualCheck();
    checkfield();
})

$(document).scannerDetection({

    timeBeforeScanTest: 200,
    avgTimeByChar: 40,
    preventDefault: false,
    endChar: [13],
    onComplete: function(barcode, qty){
        validScan = true;
        checkOrder(barcode);
    },
    onError: function(string, qty) {
        checkOrder(string);
    }
});

function checkOrder(barcode) {

    if(($("#manualCheck").data('bs.modal') || {})._isShown){ return }

    if($("#scannable").length){ 
        var curentUrl = location.origin + location.pathname + "?slip_no=" + barcode;
        window.location.href = curentUrl;
        return
    }
}

function manualCheck() {
    $('#manual-submit').on('click', function(){
        var c = $('#manual-check').val();
        if($.trim(c) == '') { return }
        window.location.href = "checking.php?slip_no=" + c;
        return
    })
}


function undoApprove(id){
    confirmed(undoApproveCallback, "Revalidate Product(s)?", "Yes", "Cancel", id);
    return
}

function undoApproveCallback(id){
    $.ajax({
        url:"controller/controller.checking.php?mode=undo_approved",
        method:"POST",
        data:{ id: id },
        success:function(){
            window.location.reload();
        }
    })
}

function approveModal(){
    $('.btn_approve').on('click',function(){
        var $el = $(this);

        $('.btn_validate').attr("data-id",$el.data('id'));
        $('.btn_validate').attr("data-weight",$el.data('weight'));
        
        $('#weight-to-compare').html($el.data('weight') + " kg");
        $('#weight-to-compare').attr("data-weight", $el.data('weight')).removeClass("is-valid is-invalid");

        $('#weightper_unit_to_compare').html($el.data('weightper_unit') + " kg");
        $('#weightper_unit_to_compare').attr("data-weightper_unit", $el.data('weightper_unit')).removeClass("is-valid is-invalid");

        $('.btn_validate').attr("data-qty_order",$el.data('qty_order'));
        $('#pick-to-compare').html($el.data('qty_order') + " " + $el.data('uom'));

        $('#one-by-total').html($el.data('qty_order'));

        $('#pick-to-compare').attr("data-qty",  $el.data('qty_order'));
        
        $('#quantity_picked, #weight_picked, #weight_picked_perUnit').val("").removeClass("is-valid is-invalid");
        $('#count_weightedItem').val("0");
        $('#one-by-pick').html('0');
        $('#check_modal').modal('show');
    });

    $('#quantity_picked').on('change, blur, keyup, input', function(){
        
        var v = $(this).val();
        var vc = $('#pick-to-compare').attr("data-qty");

        if(v=="" || v==null){
            $(this).removeClass("is-valid");
            $(this).removeClass("is-invalid");
            stepper(false);
            $('.btn_validate').hide();
            return
        }

        if(v != vc){
            $(this).removeClass("is-valid");
            $(this).addClass("is-invalid");
            $('.btn_validate').hide();
            stepper(false);
            return
        }
    
        $(this).removeClass("is-invalid");
        $(this).addClass("is-valid");
        stepper('#v-step-1');
        return
    })


    $('#weight_picked').on('change, blur, keyup, input', function(){
        var v = $(this).val();
        totalPick(v, $(this));
        return
    });

    $('#weight_picked_perUnit').on('change, blur, keyup, input', function(){
        /* single weight validation */
        var v = $(this).val();
        pickedPerUnit(v, $(this));

        return
        
    });

    $('.btn_validate').on('click',function(){

        var $el = $(this);
        var picking_order_details_id = $el.data('id');
        var quantity_order = $el.attr('data-qty_order');
        var quantity_picked = $('#quantity_picked').val();
        
        if(quantity_order != quantity_picked){
            $.Toast("Checked quantity does not match the ordered quantity.", toasts);
            return
        }

        var w = $el.attr('data-weight');

        if(!$('#weight_picked').hasClass("is-valid") && w != 0){
            $.Toast("Weight validation failed. It is either too heavy or too light.", toasts);
            return
        }

        $.ajax({
            url:"controller/controller.checking.php?mode=approve",
            method:"POST",
            data:{ id: picking_order_details_id},
            success:function(){
                window.location.reload()
            }
        });

        return
    });

    return
}

function weight_picked_function(){

        var v = $('#weight_picked').val();
        var vc = $('#weight-to-compare').attr("data-weight");

        var result = parseFloat(parseFloat(v, 10) * 100)/ parseFloat(vc, 10);

        if(result < 97 || result > 103 || isNaN(result) || !isFinite(result)) {
            $('#weight_picked').removeClass("is-valid");
            $('#weight_picked').addClass("is-invalid");
            return
        }

        $('#weight_picked').removeClass("is-invalid");
        $('#weight_picked').addClass("is-valid");
        return

}
function pack_order(slip_id){

    var itemstocheck = parseInt($('#itemstocheck').html());
    if(itemstocheck > 0 || isNaN(itemstocheck)){
        $.Toast("Unable to move this order to packer. Please validate all products first.", toasts);
        return
    }

    confirmed(pack_order_callback, "This action will send this order to packing.<br>Press 'ok' to continue.", "Ok", "Cancel", slip_id);
    return
}

function pack_order_callback(slip_id){
    $.ajax({
        url:"controller/controller.checking.php?mode=pack",
        method:"POST",
        data:{ slip_id: slip_id },
        success:function(){
            window.location.href="checking.php?slip_no=";
        }
    });
}


function reinvoice_order(slip_id){
    confirmed(reinvoice_order_callback, "This action will send back this order to invoicing. Proceed to reinvoice?", "Reinvoice", "Cancel", slip_id);
    return
}

function reinvoice_order_callback(slip_id){
    $.ajax({
        url:"controller/controller.checking.php?mode=invoice",
        method:"POST",
        data:{ slip_id: slip_id },
        success:function(){
            window.location.href="checking.php";
        }
    });
}


/* STEPPER */

$(function(){
    $('.steps').hide();
    $('#check_modal').on('hide.bs.modal', function(){
        stepper(false);
        $('#quantity_picked').val('');
        $('.btn_validate').hide();
        $('.bulk').find('input').attr('readonly', false).val('');
        $('#count_weightedItem').val('0');
        $('#one-by-pick').html('0');
        $('.total-paster').show();
        $('.total-clearer').show();
        console.log('b');
    });
    weightBranch();
})

function stepper(step){
    $('.steps').fadeOut('500');
    if(step == false) { return }
    $(step).fadeIn('500');
}

function weightBranch(){
    $('.validate-weight').on('click', function(){
        var type = $(this).data('type');
        switch(type){
            case 'bulk':
                $('.bulk').find('input').attr('readonly', false);
                $('.single').hide();
                break;
            case 'single':
                $('.bulk').find('input').attr('readonly', true);
                $('.total-paster').hide();
                $('.total-clearer').hide();
                $('.single').show();
                break;
        }
            
        stepper('#v-step-2');

        if($('.btn_validate').attr('data-weight') == 0) {
            $('#unavailable, .btn_validate').fadeIn('100');
            $('#v-step-2').fadeOut(0);
            
        }
    })

    $('.reset-validation').on('click', function(){
        loader();
        $('#check_modal').modal('hide')
        setTimeout(function(){
            $('#check_modal').modal('show')
            loader();
        }, 1000)
    })

    return
}

function checkfield(){
    $('.paster').on('click', function(){ 
        var $target = $($(this).data('to'));
        var weight = $('#weight-to-compare').html();

        var res = weight.split(" ");
        var text = res[0];

        $('#weight_picked').val(text);
        
        totalPick(text, $target);

        // navigator.clipboard.readText().then(res => {

        //     text = parseFloat(res);
        //     if(isNaN(text)) { text = 0.0; }
        //     $target.val(text);

        //     if($(this).data('to') == '#weight_picked') {
        //         return
        //     }
        // pickedPerUnit(text, $target);
        //     return
        // });
        return
    })
    $('.clearer').on('click', function(){
        var $target = $($(this).data('to'));
        $target.val(0);
        return
    })
}

function pickedPerUnit(v, el){
    var quantity_picked = $('#pick-to-compare').attr("data-qty");
    var count_weightedItem = $('#count_weightedItem').val();
        
    if(parseFloat(count_weightedItem) >= parseFloat(quantity_picked)){
        console.log(count_weightedItem);
        $.Toast("You've reached the maximum number of picked item.", toasts);
        $('#weight_picked_perUnit').val("");
        return
    }

    if(v=="" || v==null){
        el.removeClass("is-valid");
        el.removeClass("is-invalid");
        $('.btn_validate').hide();
        return
    }

    var vc = $('#weightper_unit_to_compare').attr("data-weightper_unit");
    var result = parseFloat(parseFloat(v, 10) * 100) / parseFloat(vc, 10);

    if(result < 97 || result > 103 || isNaN(result) || !isFinite(result)) {
        el.removeClass("is-valid");
        el.addClass("is-invalid");
        $('.btn_validate').hide();
        $.Toast("Invalid weight", toasts);
        return
    }

    $.Toast(v + " added to gross weight.", toasts_correct);
    el.removeClass("is-invalid");
    // $(this).addClass("is-valid");
    var total_weight = $('#weight_picked').val();
    
    if(total_weight==null || total_weight==""){
        total_weight = 0;
    }
    total_weight = parseFloat(total_weight,10)  + parseFloat(v,10);
    count_weightedItem = parseFloat(count_weightedItem,10) + 1;
    $('#count_weightedItem').val(count_weightedItem);
    $('#one-by-pick').html(count_weightedItem);
    $('#weight_picked').val(total_weight).trigger('input');

    setTimeout(function(){
            $('#weight_picked_perUnit').val('');
            weight_picked_function();
    }, 100);
}

function totalPick(v, el){
    if(v=="" || v==null){
        el.removeClass("is-valid");
        el.removeClass("is-invalid");
        $('.btn_validate').hide();
        return
    }
    
    var vc = $('#weight-to-compare').attr("data-weight");
    var result = parseFloat(parseFloat(v, 10) * 100)/ parseFloat(vc, 10);

    if(result < 97 || result > 103 || isNaN(result) || !isFinite(result)) {
        el.removeClass("is-valid");
        el.addClass("is-invalid");
        $('.btn_validate').hide();
        return
    }
    $('.btn_validate').show();
    el.removeClass("is-invalid");
    el.addClass("is-valid");
}