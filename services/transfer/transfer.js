$(function(){

    ajaxForm();
    transferTable();
    // unitModal();
    requestee();
})


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
                $('#transferTable').DataTable().ajax.reload();
                $inputs.prop("disabled", false);
            }
        })
        return false;
    })
}


function reviewTransfer(id,statuss){
    confirmed(reviewTransferCallback, "Do you really want to <span class='text-primary'>"+statuss+"</span> this?", "Yes", "Cancel", [id, statuss]);
    return
}

function reviewTransferCallback(params){
    $.ajax({
        url:"controller/controller.transferitem.php?mode=review",
        method:"POST",
        data:{ id: params[0], statuss: params[1] },
        success:function(data){
            var data = JSON.parse(data);
            $.Toast(data.message, {
                'width': 0,
                'duration': 1000,
                'position': 'top',
                'align': 'right',
                'zindex': 99999
            });
            transferTable();
        }
    });
}

function deleteTransfer(id){
    confirmed(deleteTransferCallback, "Delete this Transfer Request?", "Delete", "Cancel", id);
    return
}

function deleteTransferCallback(id){
    $.ajax({
        url:"controller/controller.transferitem.php?mode=delete",
        method:"POST",
        data:{
            id: id
        },success:function(){
            $.Toast("Successfully Deleted", {
                'width': 0,
                'duration': 1000,
                'position': 'top',
                'align': 'right',
                'zindex': 99999
            });
            transferTable();
        }
    });
}

function transferTable(){

    $('#transferTable').DataTable().destroy();
    $('#transferTable').DataTable({
        "bLengthChange": false,
        "bSort":true,
        "pageLength": 15,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "ajax" : "controller/controller.transferitem.php?mode=table",
        "columns" : [
            { "data" : "product_code"},
            { "data" : "product_description"},
            { "data" : "location"},
            { "data" : "stock_qty_moving"},
            { "data" : "moving_to"},
            { "data" : "requested_by"},
            { "data" : "transfer_status"},
            { "data" : "action"}
        ]
    });

    $('#dataTableSearch').on('keyup', function(){
        $('#transferTable').DataTable().search($(this).val()).draw();
    });

}


function requestee() {
    $.ajax({
        url: "controller/controller.transferitem.php?mode=getraks",
        method: "get",
        success: function(data) {
            var data = JSON.parse(data);
            $('#raks').html(data.view);    
        }
    })

    $('body').on('click', '.requestee',  function() {
        var requestId = $(this).data('id');
        var toBreak = $(this).data('to');
        var toMax = $(this).data('max');

        $.ajax({
            url: "",
            method: "get",
            success: function(data) {
                $("#qty").val(toMax).attr('max', toMax).attr('placeholder', toMax);
                $("#req_id").val(requestId);
                $('#to-break').html(toBreak);
                $('#request').modal('show');
            }
        })

    })
}

function breakdown(){

    var error = false;
    
    if($('#req_id').val() == ""){ error = true; }
    if($('#qty').val() == ""){ error = true; }
    if($('#raks').val() == ""){ error = true; }

    if(error) {
        $.Toast("Please fill in all required field.");
        return
    }

    $.ajax({
        url:"controller/controller.transferitem.php?mode=request",
        method:"POST",
        data:{
            request_id : $('#req_id').val(),
            to_move : $('#qty').val(),
            to_rak : $('#raks').val()
        },
        success:function(){
            $.Toast("Successfully Added", {
                'width': 0,
                'duration': 1000,
                'position': 'top',
                'align': 'right',
                'zindex': 99999
            });
            $('#request').modal('hide');
            transferTable();
        }
    });
}