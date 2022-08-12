var errorToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-danger"}
var successToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-primary"}

$(function() {
    $('.pcode').select2({ width: "resolve" });

    $('#product_codes').load('controller/controller.inout.php?mode=getAllProductCode');


    $('#product_codes').change(function() {
        var product_id = $(this).val();

        $.ajax({
            url: 'controller/controller.inout.php?mode=searchProductUnit',
            method: 'POST',
            data: {
                product_id:product_id
            },
            success:function(data) {
                var obj = $.parseJSON(data);

                $('#unit').val(obj.unit);

                $('#lotno').load('controller/controller.inout.php?mode=getLotnumber&product_id='+product_id);
                var lotno = $('#lotno option:selected').val();

                if(lotno != "") {
                    $('#qty_per_lot').val("");
                    $('#exp_date').val("");
                }

                if(obj.quantity == null){
                    $('#stock_quantity').val(0);
                } else {
                    $('#stock_quantity').val(obj.quantity);
                }
            }
        });
		$('#lotno').load('controller/controller.addorder.php?mode=getLotnumber&product_id='+product_id);
    })

    $('#lotno').change(function() {
        var stock_id = $(this).val();
    
        $.ajax({
            url: 'controller/controller.inout.php?mode=getExpirationDate',
            method: 'POST',
            data: {
                stock_id:stock_id
            },
            success:function(data) {
                var obj = $.parseJSON(data);
    
                $('#qty_per_lot').val(obj.log_qty);
                $('#transac_date').val(obj.transac_date);
    
                if(obj.exp_date == "0000-00-00") {
                    $('#exp_date').val("N/A");
                } else {
                    $('#exp_date').val(obj.exp_date);
                }
    
            }
        });
    })

    $('#submitBtn').on('click', function() {
        var pcode = $('#product_codes').val();
        var unit = $('#unit').val();
        var stockQuantity = $('#stock_quantity').val();
        var lotno = $('#lotno').val();
        var quantityPerLot = $('#qty_per_lot').val();
        var expDate = $('#exp_date').val();
        var quantity = $('#quantity').val();
        var transacDate = $('#transac_date').val();

        var totalQuantity = quantityPerLot - quantity;

        if (totalQuantity >= 0) {
            if (quantity == "") {
                $.Toast("Please double check required field", errorToast);
            } else {
                submit(pcode, unit, stockQuantity, lotno, expDate, totalQuantity, quantity, transacDate,)
            }
        } else if (totalQuantity <= 0) {
            $.Toast("Invalid Quantity", errorToast);
        }

    });

    var status_module = window.localStorage.getItem("stat");
    if (status_module == "sucess") {
        $.Toast("Successfully", successToast);
        localStorage.clear();
    }
});

function submit(pcode, unit, stockQuantity, lotno, expDate, totalQuantity, quantity, transacDate) {
    $.ajax({
        url: 'controller/controller.inout.php?mode=updateQuantity',
        method: 'POST',
        data: {
            pcode:pcode,
            unit:unit,
            stockQuantity:stockQuantity,
            lotno:lotno,
            expDate:expDate,
            totalQuantity:totalQuantity,
            quantity:quantity,
            transacDate:transacDate
        },
        success:function() {
            window.localStorage.setItem("stat", "sucess");
            window.location.href="inout.php";
        }
    });
}

// function searchValue(str) {
//     if(str.length == 0) {
//         $('#product_codes').load('');
//         $('#unit').val("");
//         $('#stock_quantity').val("");
//         $('#qty_per_lot').val("");
//         $('#exp_date').val("");
//         $('#lotno').load('');
//         $('#transac_date').val('');
//         return;
//     } else {
//         var xmlhttp = new XMLHttpRequest();
//         xmlhttp.onreadystatechange = function() {
//             if (this.readyState == 4 && this.status == 200) {
//                 var responseVal = this.responseText;

//                 var obj = $.parseJSON(responseVal);
//                 $('#product_codes').load('controller/controller.inout.php?mode=getProductCode&product_code='+obj.product_code);

//                 $.ajax({
//                     url: 'controller/controller.inout.php?mode=searchProductUnit',
//                     method: 'POST',
//                     data: {
//                         product_code:obj.product_code
//                     },
//                     success:function(data) {
//                         var obj = $.parseJSON(data);

//                         $('#unit').val(obj.unit);

//                         $('#lotno').load('controller/controller.inout.php?mode=getLotnumber&product_id='+obj.id);
//                         var lotno = $('#lotno option:selected').val();

//                         if(lotno != "") {
//                             $('#qty_per_lot').val("");
//                             $('#exp_date').val("");
//                         }

//                         if(obj.quantity == null){
//                             $('#stock_quantity').val(0);
//                         } else {
//                             $('#stock_quantity').val(obj.quantity);
//                         }
//                     }
//                 });
//             }
//         }
//         xmlhttp.open("GET", "controller/controller.inout.php?mode=searchCode&product_code="+str, true);
//         xmlhttp.send();
//     }
// }

// $('#product_codes').change(function() {
//     var product_id = $(this).val();

//     $.ajax({
//         url: 'controller/controller.inout.php?mode=getProductUnit',
//         method: 'POST',
//         data: {
//             product_id:product_id
//         },
//         success:function(data) {
//             var obj = $.parseJSON(data);

//             $('#unit').val(obj.unit);

//             $('#lotno').load('controller/controller.inout.php?mode=getLotnumber&product_id='+product_id);
//             var lotno = $('#lotno option:selected').val();

//             if(lotno != "") {
//                 $('#qty_per_lot').val("");
//                 $('#exp_date').val("");
//             }

//             if(obj.quantity == null){
//                 $('#stock_quantity').val(0);
//             } else {
//                 $('#stock_quantity').val(obj.quantity);
//             }
//         }
//     });
// })


function quantityVal(quantity) {
    if(quantity <= 0) {
        $('#quantity').val('');
    }
}