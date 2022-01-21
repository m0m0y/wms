var errorToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-danger"}
var successToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-primary"}
var receivecache;
var updatecache;
var updatetarget;

$(document).scannerDetection({
    timeBeforeScanTest: 200,
    avgTimeByChar: 40,
    endChar: [13],
    onComplete: function(barcode){
        validScan = true;
        prepareScan(barcode);
    },
    onError: function(string) {
        prepareScan(string);
    }
});

$(function(){
    pickreport();
    editCount();
})

function prepareScan(string){
    if(($("#validity-modal").data('bs.modal') || {})._isShown){ return }
    if($('.receive-item').is(':focus')) {
        receiveItem(string);
        return
    }
    toaster("Please select item to receive first", 'bg-danger');
}

function pickreport(){
    $('.pick-report').on('click', function(){
        fetchReport($(this).data('id'));
    })
}

function fetchReport(id){
    loader();
    $.ajax({
        url:"controller/controller.receiving.php?mode=fetch&id=" + id,
        method:"GET",
        success: function(data){
            $('#user-picking').html(data).addClass('active');
            loader();
            return
        }
    });
    return
}

function receiveItem(string){
    var lot = $('.receive-item:focus').data('update');
    if(typeof lot === 'undefined') {
        toaster("Please select item to receive first", 'bg-warning');
        return false;
    }

    if(lot.toString().toLowerCase() !== string.toString().toLowerCase()) {
        audioTrigger('#audio_incorrect');
        $('#validity-fail').modal('show');
        return false;
    }

    audioTrigger('#audio_correct');
    receivecache = string;
    $('#validity-modal').modal('show');
    return true;
}


function toaster(message, classes) {
    $.Toast(message, {
        'duration': 4000,
        'position': 'top',
        'align': 'right',
        'class': classes
    });
}

function audioTrigger(e){
    $(e)[0].play();
    return
}

function receive(){

    var qty = parseInt($('#pickingQuantity').val(), 10);
    var cur = parseInt($('#'+receivecache).html(), 10);
    var id = $('#'+receivecache).data('id');
    var latest = qty + cur;

    if(isNaN(qty) || isNaN(latest)){
        toaster("Invalid Quantity", 'bg-warning');
        return
    }

    loader();
    $.ajax({
        url:"controller/controller.receiving.php?mode=add-qty&id=" + id + "&qty=" + latest,
        method:"GET",
        success: function(){
            loader();
            $('#validity-modal').modal('hide');
            $('#'+receivecache).html(latest);
            return
        }
    });
    return
}

function finishReceiving(id){
    confirmed(finishedReceiving, "Are you done counting the items to receive ?", "Yes", "Cancel", id);
    return;
}

function finishedReceiving(id){
    loader();
    $.ajax({
        url:"controller/controller.receiving.php?mode=finish&id=" + id,
        method:"GET",
        success: function(){
            window.location.reload();
        }
    })
}

function editCount(){
    $('body').on('click', '.control-anchor', function(){
        var $this = $(this);
        var cur = parseInt($('#'+$this.data('update').toString().toLowerCase()).html(), 10);
        var id = $this.data('id');
        $('#editQty').val(cur);
        updatecache = id;
        updatetarget = $this.data('update').toString().toLowerCase();
        $('#edit-modal').modal('show');
    })
}

function updateCount(){
    
    var qty = parseInt($('#editQty').val(), 10);

    if(isNaN(qty)){
        toaster("Invalid Quantity", 'bg-warning');
        return
    }

    loader();
    
    $.ajax({
        url:"controller/controller.receiving.php?mode=add-qty&id=" + updatecache + "&qty=" + qty,
        method:"GET",
        success: function(){
            loader();
            $('#edit-modal').modal('hide');
            $('#'+updatetarget).html(qty);
            return
        }
    });
    return
}