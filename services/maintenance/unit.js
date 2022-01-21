$(function(){

    $('#li_unit').addClass('active');

    unitTable();
    unitModal();
})


function unitModal(){
    /* revert to add form on modal close */
    $('#unitModal').on('hide.bs.modal', function(){
        $('#unitForm').attr('action', 'controller/controller.unit.php?mode=add');
        $('#operation').html('Add');
        $('#unit_id').val('');
        $('#unit_name').val('');
    })
}

function updateUnit(id, name){
    $('#unitForm').attr('action', 'controller/controller.unit.php?mode=update');
    $('#unit_id').val(id);
    $('#unit_name').val(name);
    $('#unitModal').modal('show');
}

function deleteUnit(id, name){
    $('#unit_id_to_delete').val(id);
    $('#deleteName').text(name);
    $('#unitDelete').modal('show');
}

function unitTable(){
    $('#unitTable').DataTable().destroy();
    $('#unitTable').DataTable({
        "bLengthChange": false,
        "pageLength": 5,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "ajax" : "controller/controller.unit.php?mode=table",
        "columns" : [
            { "data" : "unit_name"},
            { "data" : "action"}
        ]
    });

    $('#dataTableSearch').on('keyup', function(){
        $('#unitTable').DataTable().search($(this).val()).draw();
    });
}