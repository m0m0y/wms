var errorToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-danger"}
var invalidQty = {'width': 0, 'duration': 900, 'position': 'top', 'align': 'right', 'zindex': 99999}
$(function(){
	customFileInput();
	ajaxForm();

	$('#addorder_btn').attr('disabled', 'disabled');

	$('#product_codes').load('controller/controller.inout.php?mode=getAllProductCode');

	$('#product_codes').change(function() {
        var product_id = $(this).val();
		$('#lotno').load('controller/controller.addorder.php?mode=getLotnumber&product_id='+product_id);
    });

	$('#lotno').change(function() {
		var stock_id = $(this).val();
		$.ajax({
			url: "controller/controller.addorder.php?mode=getLotQty&stock_id="+stock_id,
			method: "GET",
			data: { stock_id:stock_id },
			success: function(data) {
				var stock_qty = JSON.parse(data);
				$('#stock_qty').val(stock_qty);
				$('#order_qty')
					.attr('placeholder', stock_qty)
					.val(stock_qty)
					.attr("max", stock_qty);

				validateInput('#order_qty');
			}
		});
	});

	$('.pcode').select2({
		width: "resolve"
	});

	$('#preview_btn').on('click', function() {
		var id = $('#product_codes').val();
		var order_qty = $('#order_qty').val();
		var lot_no = $('#lotno').val();
		var location = $('#location').val();

		var lotname = $('#lotno option:selected').text();
		var lotno_res = lotname.split(" ");

		if(lot_no == null || lot_no == "" || order_qty == "") {
			$.Toast('Please check required field', errorToast);
			return
		} else {
			if(order_qty > stock_qty) {
				$.Toast('No stocks available', invalidQty);
				$('#addorder_btn').attr('disabled', 'disabled');
				return
			}
			if (order_qty <= - 0) {
				$.Toast('Invalid Quantity!', invalidQty);
				$('#addorder_btn').attr('disabled', 'disabled');
				return
			} 
			else {
				$.ajax({
					url: 'controller/controller.product.php?mode=get&id='+id+'&lot_id='+lot_no,
					method: 'POST',
					data: { id:id },
					success: function(data) {
						var data = JSON.parse(data);
						$('.order-container').append('<div id="ss'+data[0]+lot_no+order_qty+'" style="margin-bottom: 3px;"><div class="d-none"><input type="text" name="product_codes[]" value="'+data[0]+'"><input type="text" name="order_qty[]" value="'+order_qty+'"><input type="text" name="lotno[]" value="'+lotno_res[1]+'"><input type="text" name="location[]" value="'+location+'"></div> <span class="bg-primary item-details text-white px-2"><a type="button" onclick="remove_btn('+data[0]+','+lot_no+','+order_qty+')" id="remove_btn"><i class="material-icons">close</i></a> '+order_qty+' '+data[5]+' <small>('+lotno_res[1]+')</small></span></div> ');
					}
				});
			}
			$('#addorder_btn').removeAttr('disabled');
		}
	});
})

function remove_btn(data, lot_no, order_qty) {
	var div = 'div #ss'+data+lot_no+order_qty;
	$(div).remove();
	$('#addorder_btn').attr('disabled', 'disabled');
}


function customFileInput(){
	$('.image-area').on('click', function(){
		$('#'+$(this).data('target')).trigger('click');
		$('#'+$(this).data('target')).on('change', function(){ 
			$('#upload-form').submit();
			return
		})
	})
	return
}

function addOrdersManual() {
	$('#addOrderForm').modal('show');
}

function ajaxForm(){
	$('#upload-form').on('submit', function(e){

		e.preventDefault();

		loader();
		
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
				
				var data = JSON.parse(data);

				loader();
				
				$inputs.prop("disabled", false);
				$inputs.off('change');
				$inputs.val('');

				$.Toast(data.message, {
                    'width': 0,
                    'duration': 4000,
                    'position': 'top',
                    'align': 'right',
                    'zindex': 99999
				});
			}
		})
		return false;
	})

	$('#orderForm').on('submit', function(e){
		e.preventDefault();

		var $inputs = $(this).find("input, select, button, textarea");
		var action = $(this).attr("action");
		var type = $(this).attr("method");
		var formData = new FormData(this);

		// var id = $('#product_codes').val();
		// var order_qty = $('#order_qty').val();
		// var lot_no = $('#lotno').val();

		// if(id == null || order_qty == null || lot_no == null) {
		// 	$.Toast('Please check required field', errorToast);
		// }
		// if(order_qty <= 0) {
		// 	$.Toast('Invalid Quantity', {
		// 		'width': 0,
		// 		'duration': 4000,
		// 		'position': 'top',
		// 		'align': 'right',
		// 		'zindex': 99999
		// 	});
		// } else {
			loader();
			$.ajax({
				url: action,
				type: type,
				data: formData,
				processData: false,
				contentType: false,
				success: function(data) {
					
					var data = JSON.parse(data);
	
					loader();
					
					$inputs.prop("disabled", false);
					$inputs.off('change');
					$inputs.val('');
	
					$.Toast(data.message, {
						'width': 0,
						'duration': 4000,
						'position': 'top',
						'align': 'right',
						'zindex': 99999
					});
	
					$('div .order-container').remove();
				}
			})

		// }

	})

}

function clr_btn() {
	$('.pcode').val(null).trigger('change');
	$('#location, #order_qty, #stock_qty').val("");
}

function validateInput (selector) {
    $(selector).on('input, blur, keyup', function () {
        const val = $(this).val()
        const max = $(this).attr('max')
        if (val > max) { 
            $.Toast("You are picking items more than what you need!", {
                'duration': 4000,
                'position': 'top',
                'align': 'right',
            });
            $(this).val(max)
        }
    });
}