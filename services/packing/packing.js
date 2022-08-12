var errorToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-danger"}
var successToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-primary"}

var toasts = { 'duration': 4000,'position': 'top','align': 'right','class' : 'bg-danger','zindex': 99999 };
var toasts_correct = { 'duration': 4000,'position': 'top','align': 'right','class' : 'bg-primary','zindex': 99999 };

$(function(){
    boxItems();
    printBoxLabel();
    manualCheck();
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
        window.location.href = "packing.php?slip_no=" + c;
        return
    })
}


function dummyImage(image) {
    image.onerror = "";
    image.src = "product_image/dummy.jpg";
    return true;
}

function boxItems() {
    $button = $('.box-item');

    $button.on('click', function(){
        
        var $gallery = $('#selected-to-box-gallery');
        var $targets = $(this).data('target');
        var slip = $(this).data('slip');
        var picking_order_ids = [];
        var picking_order_names = [];
        var maxBox = parseFloat($(this).data('max')) + 1;

        $gallery.html("");

        if(!$('input.'+$targets+':checked').length){
            toast("Please select item(s) to pack.", errorToast);
            return
        }

        $.each($('input.'+$targets+':checked'), function(){      
            picking_order_ids.push($(this).val());
            picking_order_names.push($(this).data('name'));
            var $galleryImage = "<div class='col col-12 col-md-3 gal-box'><img onerror='dummyImage(this)' src='product_image/"+ $(this).data('name') +".jpg?v=1' alt='gallery-image' class='d-block w-100 border' style='margin-bottom: 15px' /></div>";
            $galleryImage += "<div class='col col-12 col-md-9'><div style='margin-bottom: 15px' ><small>LN: "+$(this).data('lot')+"</small><br>"+$(this).data('detail')+"<br><small>"+$(this).data('uom')+"</small></div></div>";
            $gallery.append($galleryImage);
            console.log('checked');
        });

        $('.gal-box').css({

        })
        
        picking_order_ids = picking_order_ids.join(",");
        picking_order_names = picking_order_names.join(", ");

        $("#toBox").val(picking_order_ids);
        $("#toName").val(picking_order_names);
        $('#noBox').val(slip + '-' + maxBox);
        $('#boxModal').modal('show');

    })

    $("body").on('click', '.unable', function(){
        toast("Please pack all items first.", errorToast);
    })

    $('#boxItem').on('click',function(){

        var box_number = $('#noBox').val();
        var picking_order_ids = $('#toBox').val();

        if($.trim(box_number) == "" || $.trim(picking_order_ids) == "") {
            toast("Please select item(s) to pack, and fill up all required field(s).", errorToast);
            return
        }
    
        $.ajax({
            url:"controller/controller.packing.php?mode=boxing",
            method:"POST",
            data:{
                box_number: box_number,
                picking_order_ids: picking_order_ids
            },
            success:function(){
                // var no_slip = $('#no_slip').val();
                
                // const url = "packing.php?slip_no="+no_slip;

                // if(isElectron()) {
                //     embedpdf(url, '.main-content')
                //     return
                // }
                
                // window.open(url);

                window.location.reload()
                return;
            }

        });
    });

}

function printBoxLabel(){

    $(".print-label").each(function(){
        var has = false;
        it = $(this);
        $pending = $("."+it.data("target"));
        if($pending.length){ has = true; }
        if(has){ it.fadeOut(500); }
    })

    $(".undo-all").each(function(){
        var has = false;
        it = $(this);
        $pending = $("."+it.data("target"));
        if($pending.length){ has = true; }
        if(has){ it.fadeOut(500); }
    })

    $(".to-ship").each(function(){
        var has = false;
        it = $(this);
        $pending = $("."+it.data("target"));
        if($pending.length){ has = true; }
        if(has){ it.addClass("unable"); }
    })

    $(".box-item").each(function(){
        var has = false;
        it = $(this);
        $pending = $("."+it.data("target"));
        if($pending.length==0){ it.fadeOut(500); }
    })

    $(".print-label").on('click', function(){
        $("#slipno").val($(this).data("slip_no"));
        $("#shipto").val($(this).data("ship_to"));
        $("#page").val($(this).data("total"));
        var page = $(this).data("total");
        $("#box-weight").attr("placeholder", "Box 1 Weight");
        $("#box-weight").val("");
        $("#remarks").val("");
        var a = 0;
        var b = 2;
        var c = 2;
        for(a=1;a<page;a++){
            $("#box-weight"+b+"").remove();
            $("#box-weight"+b+"").val("");
            b++;
        }
        
        for(a=1;a<page;a++){
            var id = "box-weight"+c;
            $("#box-weight").clone().removeAttr("id").prop('id',id).appendTo("#shipping_content");
            $("#box-weight"+c+"").attr("placeholder", "Box " + c + " Weight");
            c++;
        }
        
        

        $("#billto").val($(this).data("bill_to"));
        $('#invoiceno').val($(this).data("invoice_no"));
        $('#printModal').modal('show');
    })

    $('#printLabel').on('click', function(){
        var remarks = $('#remarks').val();
        var courier = $('#courier').val();
        var slip_no = $("#slipno").val();
        var ship_to = $("#shipto").val();
        var box_w = $("input[name='box-weight[]']").map(function(){return $(this).val();}).get();
        var bill_to = $("#billto").val();
        var page = $('#page').val();
        var id_slip = $('#id_slip').val();
        var invoice_num = $('#invoiceno').val();

        // var box_w = $("#box-weight").val();
        // var bbox = $("input[name='box-weight[]']").val();

        var url = "tcpdf/examples/shipping_label.php?a="+slip_no+"&b="+ship_to+"&c="+bill_to+"&d="+remarks+"&e="+courier+"&f="+page+"&g="+id_slip+"&w="+box_w+"&h="+invoice_num;

        if(isElectron()) {
            $('#printModal').modal('hide')
            embedpdf(url, '.main-content')
            return
        } 
        window.open(url);
        return

        // $.ajax({
        //     url: "controller/controller.packing.php?mode=invoice",
        //     type: 'POST',
        //     data:{ 
        //         id_slip : id_slip,
        //         invoice_num : invoice_num 
        //     },
        //     success: function() {
        //         window.open(url);
        //         return
        //     }
        // });

    })

    $(".print-box-label").on('click', function(){
        var slip_no = $(this).data("slip_id");
        $.ajax({
            url: "controller/controller.packing.php?mode=option",
            type: 'POST',
            data:{ slip_id : slip_no },
            success: function(data) { 
                var data = JSON.parse(data)
                if($.trim(data.html) == "") {
                    toast("Please pack items first.", errorToast);
                    return
                }
                $("#boxSelect").attr("data-slip_id", slip_no);
                $('#boxSelect').html(data.html);
                $("#printBoxDetail").modal("show");
            }
        });
    })

    $(".m-print-box-label").on('click', function(){
        var box_no = $('#boxSelect').val();
        var slip_id = $("#boxSelect").data('slip_id');
        
        const url = "tcpdf/examples/orderList.php?slip_id="+slip_id+"&box_no="+box_no;

        if(isElectron()) {
            $('.modal').modal('hide')
            embedpdf(url, '.main-content')
            return
        }
        
        window.open(url);

        return
    })
}

function undoBox(id,noOfbox){

    var totalbox = $(".print-label").data("total");
    if(noOfbox < totalbox){
        toast("Can undo by last to first box no.", errorToast);
        return
    }else{
        confirmed(undoBoxCallback, "Undo item packaging ?", "Undo", "Cancel", id);
        return
    }
    
}

function undoBoxCallback(id){
    $.ajax({
        url:"controller/controller.packing.php?mode=undo",
        method:"POST",
        data:{ id:id },
        success:function(){
            var no_slip = $('#no_slip').val();
            window.location.href="packing.php?slip_no="+no_slip;
        }
    });
}

function undoallBox(slip_id){
    confirmed(undoallBoxCallback, "Undo all item packaging ?", "Undo All", "Cancel", slip_id);
    return
}

function undoallBoxCallback(slip_id){
    $.ajax({
        url:"controller/controller.packing.php?mode=undoall",
        method:"POST",
        data:{ slip_id:slip_id },
        success:function(){
            var no_slip = $('#no_slip').val();
            window.location.href="packing.php?slip_no="+no_slip;
        }
    });
}

function shipOrder(slip_id){
    var $el = $('#btn_send');
    var total = $el.data('total');
    if(total > 0){ return; }
    confirmed(shipOrderCallback, "Send this transaction to shipping?", "Sent to shipping", "Cancel", slip_id);
    return
}

function shipOrderCallback(slip_id){

    $.ajax({
        url:"controller/controller.packing.php?mode=ship",
        method:"POST",
        data:{
        slip_id: slip_id},
        success:function(){
            var no_slip = $('#no_slip').val();
            window.location.href="packing.php?slip_no=";
        }
    });
}

function recheckOrder(slip_id){
    confirmed(recheckOrderCallback, "Send this transaction back to checker?", "Send back to checker", "Cancel", slip_id);
    return
}

function recheckOrderCallback(slip_id){
    $.ajax({
        url:"controller/controller.packing.php?mode=check",
        method:"POST",
        data:{ slip_id: slip_id },
        success:function(){
            var no_slip = $('#no_slip').val();
            window.location.href="packing.php?slip_no="+no_slip;
            return
        }
    });
}


function toast(message, type){ $.Toast(message, type); }
