$(function(){
    ajaxForm();
    $('#maintenance-nav').toggleClass('active');

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
                $('#categoryTable').DataTable().ajax.reload();
                $('#unitTable').DataTable().ajax.reload();
                $('#userTable').DataTable().ajax.reload();
                $('#customerTable').DataTable().ajax.reload();
                $('#rakTable').DataTable().ajax.reload();
                $('#cartTable').DataTable().ajax.reload();
                $('#productTable').DataTable().ajax.reload();
                $('#truckTable').DataTable().ajax.reload();
                $('#StockModal').DataTable().ajax.reload();
                $('#transferTable').DataTable().ajax.reload();
                $inputs.prop("disabled", false);
            }
        })
        return false;
    })
}