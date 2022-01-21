$(function(){
    $('#li_stockcard').addClass('active');
    $('#inventory_report-nav').toggleClass('active');
    $('.select2').select2();

    generateProductSelect();


    $('#generatePDF').on('click',function(){
    	var product = $('#d_product').val();
    	var stock_id = $('#d_lotnumber').val();
        const url = "tcpdf/examples/stockcard.php?stock_id="+stock_id+"&product_id="+product;
        if(isElectron()) {
            embedpdf(url, '.main-content')
            return
        }
    	window.open(url);
        return
    });

    $('#d_product').on('change',function(){
    	var product = $('#d_product').val();
    	generateLotSelect(product);
    });

})


function generateProductSelect(){
    $.ajax({
        url: "controller/controller.report.php?mode=option_product",
        type: 'GET',
        processData: false,
        contentType: false,
        success: function(data) { 
            var data = JSON.parse(data)
            $('#d_product').append(data.html);
        }
    })
}


function generateLotSelect(product){
    $.ajax({
        url: "controller/controller.report.php?mode=option_lot",
        type: 'POST',
        data:{
        	product : product
        },
        success: function(data) { 
            var data = JSON.parse(data)
            $('#d_lotnumber').append(data.html);
        }
    })
}