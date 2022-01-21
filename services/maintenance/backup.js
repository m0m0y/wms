var errorToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-danger"}
var successToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-primary"}
$(function(){

    $('#li_backup').addClass('active');

    databaseTable();
})

function databaseTable(){
    $('#databaseTable').DataTable().destroy();
    $('#databaseTable').DataTable({
        "bLengthChange": false,
        "pageLength": 5,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "ajax" : "controller/controller.backup.php?mode=table",
        "columns" : [
            { "data" : "database_name"},
            { "data" : "database_date"},
            { "data" : "action"}
        ]
    });

    $('#dataTableSearch').on('keyup', function(){
        $('#databaseTable').DataTable().search($(this).val()).draw();
    });
}

function download_db(database_name){
    window.location.href="download.php?dbase="+database_name;
  }

function backup(){
    confirmed(backupCallback, "Do you want to generate new backup?", "Yes", "Cancel");
}

function backupCallback(){
    toggleLoad();
    $.ajax({
        url:"controller/controller.backup.php?mode=backup",
        method:"GET",
        success:function(){
            $.Toast("Backup completed successfully", {
                'width': 0,
                'duration': 1000,
                'position': 'bottom',
                'align': 'right',
                'zindex': 99999
            });
            databaseTable();
            toggleLoadClose();
        }

    });
    
}