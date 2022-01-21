$(function(){
    $('#li_summary').addClass('active');
    $('#inventory_report-nav').toggleClass('active');

    $('#overall_products').on('click',function(){
    	
    	window.open("tcpdf/examples/summary.php");

    });

    $('#overall_products_withlots').on('click',function(){
    	
    	window.open("tcpdf/examples/summarywithlots.php");

    });
})