$(function(){
    $('#li_quarantine').addClass('active');
    $('#inventory_report-nav').toggleClass('active');

    $('#generatePDF').on('click',function(){
      // const dateFrom = $('#dateFrom').val();
		  // const dateTo = $('#dateTo').val();
      // const url = "tcpdf/examples/quarantine.php?dateFrom="+dateFrom+"&dateTo="+dateTo;
      const url = "tcpdf/examples/quarantine.php";
      if(isElectron()) {
        embedpdf(url, '.main-content')
        return
      }
		  window.open(url);
      return
    });

    qurantineTable();
})


function qurantineTable() {
  $('#quarantineTable').DataTable().destroy();
    $('#quarantineTable').DataTable({
      "bLengthChange": false,
      "pageLength": 5,
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "ajax" : "controller/controller.report.php?mode=tableDetail",
      "columns" : [
          {"data": function(data){
            return data.product_code + "</br> <small>" + data.product_description + "</small>";
          }},
          { "data" : "uom"},
          { "data" : "stock_lotno"},
          { "data" : "slip_order_date"},
          { "data" : "stock_expiration_date"},
          { "data" : "stock_qty"}
      ],
  });

  $('#quarantineTable').on( 'page.dt', function () {
    $('html, body').animate({
        scrollTop: 0
    }, 500);   
  });
}