$(function(){
    sim_redirect();
    clone_item();
    $('#inventory_report-nav').toggleClass('active');
    delete_rr();
    re_rr();
    generateProductSelect();
    generateUnitsSelect();
    PickItem();

    customFileInput();
    ajaxForm1();
    var report_status = $('#report_status').val();
    RRTable(report_status);

    $('#report_status').on('change', function(){
        var report_status = $('#report_status').val();
        RRTable(report_status);
    });

    
})

function RRTable(report_status){
    $('#receivingTable').DataTable().destroy();
    $('#receivingTable').DataTable({
        "bLengthChange": false,
        "pageLength": 5,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "ajax" : "controller/controller.receiving.php?mode=table&report_status="+report_status,
        "columns" : [
            { "data" : "company_name"},
            { "data" : "reference"},
            { "data" : "control_no"},
            { "data" : "delivery"},
            { "data" : "notes"},
            { "data" : "action"}
        ]
    });

    $('#dataTableSearch').on('keyup', function(){
        $('#receivingTable').DataTable().search($(this).val()).draw();
    });
}

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
                window.location.href="receiving-report.php";
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

function PickItem(){
    $.each($('.select_item'), function(i) {


        $(this).on('change', function(){

          var item_code = $(this).val();
            $.ajax({
                url:"controller/controller.report.php?mode=select_item",
                method:"POST",
                data:{
                    item_code: item_code
                },success:function(data){
                    var b = $.parseJSON(data);
                    $("input[name='item_description[]']").eq(i).val(b.product_description);
                    $("select[name='item_unit[]']").eq(i).val(b.unit_id);
                }
            })

        });
    })
}


function generateProductSelect(){

    $.ajax({
        url: "controller/controller.report.php?mode=option_product_select",
        type: 'GET',
        processData: false,
        contentType: false,
        success: function(data) { 
            var data = JSON.parse(data);
            $("datalist[name='item_codee[]']").empty();
            $("datalist[name='item_codee[]']").append("<option value='0' selected='' disabled>Select Item Code</option>");
            $("datalist[name='item_codee[]']").append(data.html);

        }
    })
}

function generateUnitsSelect(){
    $.ajax({
        url: "controller/controller.unit.php?mode=option",
        type: 'GET',
        processData: false,
        contentType: false,
        success: function(data) { 
            var data = JSON.parse(data)
            $("select[name='item_unit[]']").empty();
            $("select[name='item_unit[]']").append("<option value='0' selected='' disabled>Select UOM</option>");
            $("select[name='item_unit[]']").append(data.html);
        }
    })
}

function sim_redirect(){
    $('.redirect').on('click', function() {
        window.location.href = $(this).data('href');
    })
    return
}

function clone_item(){

    $('.clone').on('click', function(){
        $clone = $('.main-product').clone();
        $clone.addClass('cloned').removeClass('main-product').find('input').val("");
        $("#product-receive").append($clone);
    
        $([document.documentElement, document.body]).animate({
            scrollTop: $clone.offset().top
        }, 600);
        PickItem();
    })

    $('.remove-clone').on('click', function(){
        $("#product-receive").find('.cloned').last().fadeOut(500, function(){
            $(this).remove();
        })
          
    })
}

function delete_rr(){
    $('.delete-rr').on('click', function(){
        confirmed(delete_rr_callback, "Delete this receiving report?", "Delete", "Cancel", $(this).data('id'));
    })
}

function delete_rr_callback(report_id){
    $.ajax({
        url: "controller/controller.report.php?mode=delete_rr&report_id="+report_id,
        type: 'GET',
        processData: false,
        contentType: false,
        success: function() { 
            $('#go-back').click().trigger('click');
        }
    })
}


function re_rr(){
    $('.re-rr').on('click', function(){
        confirmed(re_rr_callback, "Send this back to picker?", "Recount", "Cancel", $(this).data('id'));
    })
}

function re_rr_callback(report_id){
    $.ajax({
        url: "controller/controller.receiving.php?mode=re&id="+report_id,
        type: 'GET',
        processData: false,
        contentType: false,
        success: function() { 
            window.location.reload();
        }
    })
}

function btn_upload(){
    $('#UploadModal').modal('show');
}

function finish(report_id){
    var statuss = "Finished";
    var data = [report_id, statuss];
    confirmed(updateStatus_callback, "Do you want to finish this Receiving Report?", "Yes", "Cancel", data);
}

function incomplete(report_id){
    var statuss = "";
    var data = [report_id, statuss];
    confirmed(updateStatus_callback, "Do you want to back this Receiving Report in Pending?", "Yes", "Cancel", data);
}

function updateStatus_callback(data){

    $.ajax({
        url:"controller/controller.receiving.php?mode=updateStatus_rr",
        method:"POST",
        data:{
            report_id: data[0],
            statuss : data[1]
        },success:function(){
            window.location.href="receiving-report.php";
        }
    })

}