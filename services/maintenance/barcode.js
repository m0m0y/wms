var errorToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-danger"}
var successToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-primary"}
$(function(){

    $('#li_barcode_generator').addClass('active');

})

function generateBarcode(){
    var barcode = $('#barcode').val();
    if(!barcode){
        $.Toast("Please input data first.", errorToast); return;
        return
    }
    barcode = encodeURI(barcode);
    const url = "tcpdf/examples/barcodeUser.php?barcode="+barcode;
    if(isElectron()) {
        embedpdf(url, '.main-content');
        return
    }
    window.open(url);
    return
}