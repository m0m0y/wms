$(function() {
    finishedOrderTable();

    $('#dataTableSearch').on('keyup', function(){
        $('#finishedOrderTable').DataTable().search($(this).val()).draw();
    });
});

function finishedOrderTable(){
    $('#finishedOrderTable').DataTable().destroy();
    $('#finishedOrderTable').DataTable({
        "bLengthChange": false,
        "pageLength": 5,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "ajax" : "controller/controller.picking.php?mode=finished",
        "columns" : [
            { "data" : "slip_no"},
            { "data" : "bill_to"},
            { "data" : "ship_to"},
            { "data" : "po_no"},
            { "data" : "ship_date"},
            { "data" : "invoice_no"},
            { "data" : "order_status"},
            { "data" : "action"},
        ]
    });


    $.ajax({
        url: "controller/controller.picking.php?mode=orderAnalytics",
        type: 'GET',
        processData: false,
        contentType: false,
        success: function(data) { 
            var data = JSON.parse(data);

            $('#pick').html(data.pick);
            $('#invoice').html(data.invoice);
            $('#pack').html(data.pack);
            $('#deliver').html(data.deliver);

        }
    })
}

function orderSummary(slipid, slip_no, slip_order_date, bill_to, ship_to, po_no, ship_date, invoice_no, order_status, pcode){
    $('#orderSummaryModal').modal('show');
    var d = new Date();
    var dateAr = slip_order_date.split('-');
    var newOrderDate = dateAr[1] + "-" + dateAr[2] + "-" + dateAr[0];

    var dateArr = ship_date.split('-');
    var newShipDate = dateArr[1] + "-" + dateArr[2] + "-" + dateArr[0];

    $('#sn').text(slip_no);
    $('#in').text(invoice_no);
    $('#od').text(newOrderDate);
    $('#c_name').text(bill_to);
    $('#add').text(ship_to);
    $('#ship_date').text(newShipDate);
    
    $.ajax({
        url: "controller/controller.picking.php?mode=completeOrderDetails&si="+slipid,
        type: "GET",
        processData: false,
        contentType: false,
        success: function(data) { 
            var data = JSON.parse(data);
            var img = [];
            var product_desc = [];
            var quantity = [];
           
            $.each(data, function(index, item){
                img.push('<img id="frame" src="static/default-placeholder.png" class="preview mb-0" /> <br><br>');
                product_desc.push('<p class="m-0">'+item.product_description+'</p><p class="m-0 text-muted"><small>'+item.product_code+'</small></p><br><br>');
                quantity.push('<p class="m-0"> Item shipped: '+item.quantity_shipped+' </p><p class="m-0 text-muted"><small>('+item.stock_lotno+')</small></p> <br><br><br>');

                $("#image").html(img.join(""));
                $("#details").html(product_desc.join(""));
                $("#quantity").html(quantity.join(""));
            })
        }

    })
}