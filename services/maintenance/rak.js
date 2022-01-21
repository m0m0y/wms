$(function(){
    $('#li_rak').addClass('active');
    rakTable();
    rakModal();
})

function editRak(rak_id,rak_name,rak_column,rak_level){
    
    $('#rakForm').attr('action', 'controller/controller.rak.php?mode=update');
    $('#rak_id').val(rak_id);
    $('#rak_name').val(rak_name);
    $('#rak_column').val(rak_column);
    $('#rak_level').val(rak_level);
    $('#rakModal').modal('show');

}

function rakModal(){
    /* revert to add form on modal close */
    $('#rakModal').on('hide.bs.modal', function(){
        $('#rakForm').attr('action', 'controller/controller.rak.php?mode=add');
        $('#rak_id').val("");
        $('#rak_name').val("");
        $('#rak_column').val("");
        $('#rak_level').val("");
    })
}

function deleteRak(id, name){
    $('#rak_id_to_delete').val(id);
    $('#deleteName').text(name);
    $('#rakDelete').modal('show');
}


function rakTable(){
    $('#rakTable').DataTable().destroy();
    $('#rakTable').DataTable({
        "bLengthChange": false,
        "pageLength": 5,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "ajax" : "controller/controller.rak.php?mode=table",
        "columns" : [
            { "data" : "rak_name"},
            { "data" : "rak_column"},
            { "data" : "rak_level"},
            { "data" : "rak_labelname"},
            { "data" : "action"}
        ]
    });
    $('#dataTableSearch').on('keyup', function(){
        $('#rakTable').DataTable().search($(this).val()).draw();
    })
}