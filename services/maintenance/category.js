$(function(){

    $('#li_category').addClass('active');

    categoryTable();
    categoryModal();

})

function categoryModal(){
    /* revert to add form on modal close */
    $('#categoryModal').on('hide.bs.modal', function(){
        $('#categoryForm').attr('action', 'controller/controller.category.php?mode=add');
        $('#category_id').val('');
        $('#category_name').val('');
    })
}

function editCategory(id, name){
    $('#categoryForm').attr('action', 'controller/controller.category.php?mode=update');
    $('#category_id').val(id);
    $('#category_name').val(name);
    $('#categoryModal').modal('show');
}

function deleteCategory(id, name){
    $('#cat_id_to_delete').val(id);
    $('#deleteName').text(name);
    $('#catDelete').modal('show');
}


function categoryTable(){
    $('#categoryTable').DataTable().destroy();
    $('#categoryTable').DataTable({
        "bLengthChange": false,
        "pageLength": 5,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "ajax" : "controller/controller.category.php?mode=table",
        "columns" : [
            { "data" : "category_name"},
            { "data" : "action"}
        ]
    });

    $('#dataTableSearch').on('keyup', function(){
        $('#categoryTable').DataTable().search($(this).val()).draw();
    })
}
