var toasts = { 'duration': 4000,'position': 'top','align': 'right','class' : 'bg-danger','zindex': 99999 };

$(function(){

    returnTable();
    getQuarantineTable();
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

function manualCheck() {
    $('#manual-submit').on('click', function(){
        var c = $('#manual-check').val();
        if($.trim(c) == '') { return }
        window.location.href = "returned_invoice.php?slip_no=" + c;
        return
    })
}


function getQuarantineTable(){

    $.ajax({
        url:"controller/controller.returned.php?mode=getQuarantine",
        method:"GET",
        success:function(data){
            var b = $.parseJSON(data);
            $('#quarantineArea').val(b.code);
        }
    })

}

function returnTable(){
    $('#return_table').DataTable().destroy();
    $('#return_table').DataTable({
        "bLengthChange": false,
        "pageLength": 5,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "ajax" : "controller/controller.returned.php?mode=table",
        "columns" : [
            { "data" : "product"},
            { "data" : "lotno"},
            { "data" : "exp"},
            { "data" : "quantity"},
            { "data" : "action"}
        ]
    });

    $('#dataTableSearch').on('keyup', function(){
        $('#return_table').DataTable().search($(this).val()).draw();
    });
}

function undoReturn(stock_id,stock_qty,from_stock_id){
    var a = [stock_id,stock_qty,from_stock_id];
    confirmed(undoReturnCallback, "Do you really want to undo this entry?", "Yes", "No",a);
    return
}

function undoReturnCallback(a){
    var slip_no_value = $('.slip_no_value').html();
    var stock_id = a[0];
    var stock_qty = a[1];
    var from_stock_id = a[2];

    $.ajax({
        url:"controller/controller.returned.php?mode=undo",
        method:"POST",
        data:{
            stock_id: stock_id,
            stock_qty: stock_qty,
            from_stock_id: from_stock_id
        },success:function(){
            // alert("undo success");
            window.location.href = "returned_invoice.php?slip_no="+slip_no_value;
        }
    });

}

function checkOrder(barcode) {
    if($("#scannable").length){ 
        var curentUrl = location.origin + location.pathname + "?slip_no=" + barcode;
        window.location.href = curentUrl;
        return
    }
}


function finished_transaction(slip_id){

    confirmed(finished_transactionCallback, "This action will finished the transaction.\nPress 'ok' to continue.", "Ok", "Cancel", slip_id);
    return

}

function finished_transactionCallback(slip_id){

    $.ajax({
        url:"controller/controller.returned.php?mode=finished",
        method:"POST",
        data:{ slip_id: slip_id },
        success:function(){
            window.location.href="returned_invoice.php?slip_no=";
            return
        }
    });
    
}


function reship_Order(slip_id){

    var undoItem = $('#undoItem').val();
    if(undoItem<=0){

        confirmed(reship_OrderCallback, "This action will send back this order to shipping. Proceed to reship?", "Yes", "Cancel", slip_id);
        return


    }else{

        $.Toast("Not allowed for<br>Order with returned item", {
            'duration': 4000,
            'position': 'top',
            'align': 'right',
        });

    }
    
    
}

function reship_OrderCallback(slip_id){

     $.ajax({
         url:"controller/controller.returned.php?mode=reship",
         method:"POST",
         data:{ slip_id: slip_id },
         success:function(){
             window.location.href="returned_invoice.php?slip_no=";
             return
         }
     });



}

function returnItem(stock_id,stock_qty){

    $('#returnQty').attr("max",stock_qty).attr("min", 1);
    $('#returnQty').val(stock_qty);
    $('#returnStockid').val(stock_id);
    $('#return_modal').modal('show');

}

function quarantineItem(){
    $('#return_modal').modal('hide');

    confirmed(quarantineItem_Callback, "Send this item to quarantine area?", "Yes", "Cancel");
    return
}

function quarantineItem_Callback(){

    var returnStockid = $('#returnStockid').val();
    var returnQty = $('#returnQty').val();
    var quarantineArea = $('#quarantineArea').val();
    var slip_no_value = $('.slip_no_value').html();

    $.ajax({
        url:"controller/controller.returned.php?mode=quarantine",
        method:"POST",
        data:{
            returnStockid: returnStockid,
            returnQty: returnQty,
            quarantineArea: quarantineArea
        },success:function(){
            window.location.href = "returned_invoice.php?slip_no="+slip_no_value;
            return
        }
    })

    // alert(returnStockid+" & "+returnQty+" & "+quarantineArea+" & "+ slip_no_value);

}

function returnAll(slip_id){
    confirmed(QuarantineAllItems_Callback, "Send Order to quarantine area?", "Yes", "Cancel", slip_id);
    return
}

function QuarantineAllItems_Callback(slip_id){

    var quarantineArea = $('#quarantineArea').val();
    var slip_no_value = $('.slip_no_value').html();

    $.ajax({
        url:"controller/controller.returned.php?mode=quarantineOrder",
        method:"POST",
        data:{
            slip_id: slip_id,
            quarantineArea: quarantineArea
        },success:function(){
            window.location.href = "returned_invoice.php?slip_no="+slip_no_value;
            return
        }
    })

}