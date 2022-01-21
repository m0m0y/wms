var errorToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-danger"}
var successToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-primary"}

$(function(){
    detrackTable();
    $('#gmap').hide();
    $('#gmapclose').hide();

    $('#gmapclose').on('click',function(){
        $('#gmap').fadeOut();
        $('#gmapclose').fadeOut();
    });
});

function detrackTable(){
    $('#detrackTable').DataTable().destroy();
    $('#detrackTable').DataTable({
        "bLengthChange": false,
        "pageLength": 5,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "ajax" : "controller/controller.detrack.php?mode=table",
        "columns" : [
            { "data" : "detrack_action"},
            { "data" : "detrack_type"},
            { "data" : "detrack_slip"},
            { "data" : "detrack_date"},
            { "data" : "detrack_address"},
            { "data" : "detrack_tracking"},
            { "data" : "detrack_trackstatus"},
            { "data" : "detrack_customer"},
            { "data" : "detrack_assignto"},
            { "data" : "detrack_status"},
            { "data" : "detrack_time"},
            { "data" : "detrack_reject"},
            { "data" : "detrack_reason"},
            { "data" : "detrack_receivedby"},
            { "data" : "detrack_signature"}
        ]
    });

    $('#dataTableSearch').on('keyup', function(){
        $('#detrackTable').DataTable().search($(this).val()).draw();
    })
}

function viewDetrack(lat,long){
    
    if(lat=="" || lat==null && long=="" || long==null){
        $.Toast("Not Yet Delivered", errorToast);
    }else{
        var url = "https://maps.google.com/maps?q="+lat+","+long+"&output=embed";
        $('#gmap').attr('src', url);
        $('#gmap').fadeIn();
        $('#gmapclose').fadeIn();
    }
}

function viewImage(image){
    if(image=="" || image==null){
        $.Toast("There is no image yet.", errorToast);
    }else{
        var url = "order_files/"+image;
        $('#image_modal').modal('show');
        $('#image_order').attr('src', url);
    }
    

}

function viewLocation(lat,long){
    if(lat=="" || lat==null && long=="" || long==null){
        $.Toast("User's GPS is OFF", errorToast);
    }else{
        var url = "https://maps.google.com/maps?q="+lat+","+long+"&output=embed";
        $('#gmapuser').attr('src', url);
        $('#user_modal').modal('show');
    }
}