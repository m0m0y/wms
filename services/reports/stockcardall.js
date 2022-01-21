$(function(){
    $('#li_stockAlllot').addClass('active');
    $('#inventory_report-nav').toggleClass('active');
    $('.select2').select2();
    generateProductSelect();

    $('#generatePDF').on('click',function(){
    	const product = $('#d_product').val();
        const url = "tcpdf/examples/stockcard_all.php?product_id="+product;
        if(isElectron()) {
            embedpdf(url, '.main-content')
            return
        }
    	window.open(url);
        return
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