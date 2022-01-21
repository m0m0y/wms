$(function(){
    $('#li_customer').addClass('active');
    customerTable();
    customerModal();
})

function customerModal(){
    /* revert to add form on modal close */
    $('#customerModal').on('hide.bs.modal', function(){
        $('#customerForm').attr('action', 'controller/controller.customer.php?mode=add');
        $('#customer_id').val('');
        $('#customer_name').val('');
        $('#customer_contactno').val('');
        $('#customer_address').val('');
        
    })
}

function editCustomer(customer_id,customer_name,customer_contactno,customer_address){

    $('#customerForm').attr('action', 'controller/controller.customer.php?mode=update');
    $('#customer_id').val(customer_id);
    $('#customer_name').val(customer_name);
    $('#customer_contactno').val(customer_contactno);
    $('#customer_address').val(customer_address);
    $('#customerModal').modal('show');

}


function deleteCustomer(customer_id,customer_name){

    $('#customer_id_to_delete').val(customer_id);
    $('#deleteName').text(customer_name);
    $('#customerDelete').modal('show');

}

function customerTable(){
    $('#customerTable').DataTable().destroy();
    $('#customerTable').DataTable({
        "bLengthChange": false,
        "pageLength": 5,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "ajax" : "controller/controller.customer.php?mode=table",
        "columns" : [
            { "data" : "customer_name"},
            { "data" : "customer_contactno"},
            { "data" : "customer_address"},
            { "data" : "action"}
        ]
    });

    $('#dataTableSearch').on('keyup', function(){
        $('#customerTable').DataTable().search($(this).val()).draw();
    })
}