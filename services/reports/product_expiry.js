$(function(){
    $('#li_productExpiry').addClass('active');
    $('#inventory_report-nav').toggleClass('active');

    $('#generatePDF').on('click',function(){
      const dateFrom = $('#dateFrom').val();
		  const dateTo = $('#dateTo').val();
      const url = "tcpdf/examples/product_expiry.php?dateFrom="+dateFrom+"&dateTo="+dateTo;
      if(isElectron()) {
        embedpdf(url, '.main-content')
        return
      }
		  window.open(url);
      return
    });

})