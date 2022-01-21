

$(function(){
    $('#li_cart').addClass('active');
    cartTable();
    cartModal();
})


function cartModal(){
    $('#cartModal').on('hide.bs.modal', function(){
            
        $('#cartForm').attr('action', 'controller/controller.cart.php?mode=add')    
        $('#cart_id').val("");
        $('#location_name').val("");
        $('#location_type').val("");
    })
}

function editcart(cart_id,location_name,location_type){
    $('#cartForm').attr('action', 'controller/controller.cart.php?mode=update')
    $('#cart_id').val(cart_id);
    $('#location_name').val(location_name);
    $('#location_type').val(location_type);
    $('#cartModal').modal('show');

}

function deletecart(id, name){
    $('#cart_id_to_delete').val(id);
    $('#deleteName').text(name);
    $('#cartDelete').modal('show');
}

function cartTable(){
    $('#cartTable').DataTable().destroy();
    $('#cartTable').DataTable({
        "bLengthChange": false,
        "pageLength": 5,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "ajax" : "controller/controller.cart.php?mode=table",
        "columns" : [
            { "data" : "location_name"},
            { "data" : "location_type"},
            { "data" : "action"}
        ]
    });
    
    $('#dataTableSearch').on('keyup', function(){
        $('#cartTable').DataTable().search($(this).val()).draw();
    })
}
