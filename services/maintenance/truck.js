$(function(){
    $('#li_truck').addClass('active');
    truckTable();
    truckModal();

})

function truckModal(){
    $('#truckModal').on('hide.bs.modal', function(){
        $('#truckForm').attr('action', 'controller/controller.truck.php?mode=add');    
        $('#truck_id').val("");
        $('#truck_no').val("");
    })
}


function edittruck(truck_id,truck_no){
    $('#truckForm').attr('action', 'controller/controller.truck.php?mode=update');
    $('#truck_id').val(truck_id);
    $('#truck_no').val(truck_no);
    $('#truckModal').modal('show');

}


function deletetruck(id, name){
    $('#truck_id_to_delete').val(id);
    $('#deleteName').text(name);
    $('#truckDelete').modal('show');
}


function truckTable(){
    $('#truckTable').DataTable().destroy();
    $('#truckTable').DataTable({
        "bLengthChange": false,
        "pageLength": 5,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "ajax" : "controller/controller.truck.php?mode=table",
        "columns" : [
            { "data" : "truck_no"},
            { "data" : "action"}
        ]
    });
    
    $('#dataTableSearch').on('keyup', function(){
        $('#truckTable').DataTable().search($(this).val()).draw();
    })
}