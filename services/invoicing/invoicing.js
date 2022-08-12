var errorToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-danger"}

$(function(){
    var status_module = window.localStorage.getItem("stat");
    if (status_module == "sucess") {
        $.Toast("Save Invoice Number Sucessfully.", {
            'duration': 4000,
            'position': 'top',
            'align': 'right',
        });
        localStorage.clear();
    }
});

function check_order(slip_id){
    confirmed(check_order_callback, "Send this order to checker?", "Yes", "Cancel", slip_id);
}

function check_order_callback(slip_id){
    $.ajax({
        url:"controller/controller.invoicing.php?mode=check",
        method:"POST",
        data:{ slip_id: slip_id },
        success: function(){
            $.Toast("Order sent to Checker", {
                'duration': 4000,
                'position': 'top',
                'align': 'right',
            });
            
            $('.modal').modal('hide');
            $("#main").load(" #main > *");
            return
        }
    });
}

function repick_order(slip_id){
    $('#slip_id').val(slip_id);
    $('#repickModal').modal('show');
}

function repick(){

    var slip_id = $('#slip_id').val();
    var repick_comments = $('#repick_comments').val();

    $.ajax({
        url:"controller/controller.invoicing.php?mode=repick",
        method:"POST",
        data:{ 
            slip_id: slip_id, 
            comments: repick_comments 
        },
        success:function(){

            $.Toast("Order sent back to picking.", {
                'duration': 4000,
                'position': 'top',
                'align': 'right',
            });
                
            $('.modal').modal('hide');
            $("#main").load(" #main > *");
            return
        }
    });
}

function print_barcode(slip_id,slip_no){ window.open("tcpdf/examples/invoice.php?stock_lotno="+slip_no); }

$(function(){

    viewInvoice();

    setInterval(() => {
        getNewInvoices();
    }, 5000);

})

function viewInvoice() {
    $('.view-invoice').on('click', function(){
        $('#view-trigger').fadeOut('500');
        var s = '.i-view[data-target="' + $(this).data('target') + '"]';
        $(s)
            .clone()
            .appendTo('#viewing-invoice')
            .fadeIn('500')    
        ;
    })
}

function resetInvoice() {
    $('#viewing-invoice').empty();
    $('#view-trigger').fadeIn('500');
}

function getNewInvoices() {
    var total_current_invoice = $('.view-invoice').length;
    $.ajax({
        url:"controller/controller.invoicing.php?mode=count",
        method:"POST",
        success: function(data) {
            var data = JSON.parse(data);
            newInvoices(data.count, total_current_invoice);
        }    
    });
}

function newInvoices(count, current) {
    if(count > current) {
        var count = parseInt(count) - parseInt(current);
        if( $("#new-notif").length ) {
            $('#newCount').html(count);
        }
        else {
            var $notif = '<div class="col p-0" id="new-notif"><div class="card-panel p-4 border-0 empty rounded-lg">';
            $notif += '<p class="m-0 text-muted"><small>Notification</small></p>';
            $notif += '<p class="m-0 font-weight-bold">There are <span class="text-primary" id="newCount">'+count+'</span> new orders to invoice</p>';
            $notif += '<a href="invoicing.php" class="btn mt-2 btn-sm btn-primary">Refresh</a></div></div>';
            $('#invoice-grid').prepend($notif);
        }
    }

    return

}

function add_invoice(slip_id) {
    $('#slipid').val(slip_id);
    $('#addInvoice').modal('show');
}

function save_invoice() {
    var slipid = $('#slipid').val();
    var invoiceno = $('#invoiceno').val();

    if (slipid == null || invoiceno == null || invoiceno == "") {
        $.Toast("Please enter Invoice", errorToast);
    } else {
        $.ajax({
            url:"controller/controller.invoicing.php?mode=invoice",
            method:"POST",
            data:{ 
                slipid: slipid, 
                invoiceno: invoiceno 
            },
            success:function(){
                window.localStorage.setItem("stat", "sucess");
                window.location.href="invoicing.php";
                $('.modal').modal('hide');
                // $("#main").load(" #main > *");
                return
            }
        });
    }
}