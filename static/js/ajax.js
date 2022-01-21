var errorToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-danger"}
var successToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-primary"}

$(document).ready(function(){
    ajaxForm();
});

function scan_login(){
    $(document).scannerDetection({
	   
        timeBeforeScanTest: 200,
        avgTimeByChar: 40,
        endChar: [13],
        onComplete: function(barcode, qty){
            validScan = true;
            prepareScan(barcode);
        },
        onError: function(string, qty) {
            prepareScan(string);
        }
    });

    $('#validity-modal').modal('show');
}

function input_login(){
    $(document).scannerDetection(true);
    $(document).scannerDetection(false);
    $('#login_option').hide();
    $('#login_input').show();
}

function prepareScan(barcode){
    
    var useraccount = barcode;
    var userdetails = useraccount.split("/j/j/j");
    var username = userdetails[0];
    var password = userdetails[1];
    $.ajax({
        url:"controller/controller.user.php?mode=login",
        method:"POST",
        data:{
            username: username,
            password: password
        },success:function(data){
            var b = $.parseJSON(data);
            if(b.message=="Invalid login credentials"){
                $.Toast(b.message, errorToast); return;
            }else{
                toggleLoad();
                setTimeout(function(){
                    window.location.href="index.php";
                }, 1000);
                return;
                
            }
            
        }
    });

}

function ajaxForm(){

    $('.ajax-form').on('submit', function(e){

        e.preventDefault();

        var $inputs = $(this).find("input, select, button, textarea");
        var action = $(this).attr("action");
        var type = $(this).attr("method");
        var formData = new FormData(this);

        console.log("submitting form");

        $inputs.prop("disabled", true);

        $.ajax({
            url: action,
            type: type,
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) { 
                $inputs.prop("disabled", false);
                dat = JSON.parse(data);
                if(dat.code == 55) {
                    ajaxToast(dat.code, dat.message, dat.path);
                    return
                }
                ajaxToast(dat.code, dat.message);
                return;
            }
        })

        return false;
    })
}


function ajaxToast(code, message, path = false){

    var errorToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-danger"}
    var successToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-primary"}

    switch(code){
        case 0:
            style = errorToast;
            break;
        case 5:
            window.location.reload();
            return;
        case 55:
            window.location.href = path;
            return
        default:
            style = successToast;
    }

    $.Toast(message, style);
    return
}