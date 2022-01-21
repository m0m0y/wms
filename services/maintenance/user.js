$(function(){
    $('#li_users').addClass('active');
    userTable();
    userModal();
})

function userModal(){
    /* revert to add form on modal close */
    $('#userModal').on('hide.bs.modal', function(){
        $('#userForm').attr('action', 'controller/controller.user.php?mode=add');
        $('#user_id').val('');
        $('#user_fullname').val('');
        $('#user_username').val('');
        $('#user_password').val('');
        $('#user_usertype').val('');
    })
}

function editUser(user_id,user_fullname,user_username,user_password,user_usertype){
    
    $('#userForm').attr('action', 'controller/controller.user.php?mode=update');
    $('#user_id').val(user_id);
    $('#user_fullname').val(user_fullname);
    $('#user_username').val(user_username);
    $('#user_password').val(user_password);
    $('#user_usertype').val(user_usertype);
    $('#userModal').modal('show');

}

function deleteUser(id, name){
    $('#user_id_to_delete').val(id);
    $('#deleteName').text(name);
    $('#userDelete').modal('show');
}

function userTable(){
    $('#userTable').DataTable().destroy();
    $('#userTable').DataTable({
        "bLengthChange": false,
        "pageLength": 5,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "ajax" : "controller/controller.user.php?mode=table",
        "columns" : [
            { "data" : "user_fullname"},
            { "data" : "user_username"},
            { "data" : "user_password"},
            { "data" : "user_usertype"},
            { "data" : "action"}
        ]
    });

    $('#dataTableSearch').on('keyup', function(){
        $('#userTable').DataTable().search($(this).val()).draw();
    })
}

function barcodeUser(username,password){
    var barcodeUser = username+"***"+password;
    barcodeUser = encodeURI(barcodeUser);
    const url = "tcpdf/examples/barcodeUser.php?barcode="+barcodeUser;
    if(isElectron()) { embedpdf(url, '.main-content'); return }
    window.open(url);
    return   
}