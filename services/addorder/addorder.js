var errorToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-danger"}
$(function(){
	customFileInput();
	ajaxForm();

	$('#product_codes').load('controller/controller.inout.php?mode=getAllProductCode');

	$('#product_codes').change(function() {
        var product_id = $(this).val();
		$('#lotno').load('controller/controller.addorder.php?mode=getLotnumber&product_id='+product_id);
    })

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

		if(lot_no == null || lot_no == "" || order_qty == null) {
			$.Toast('Please check required field', errorToast);
		} else {

			$.ajax({
				url: 'controller/controller.product.php?mode=get&id='+id,
				method: 'POST',
				data: { id:id },
				success: function(data) {
					var data = JSON.parse(data);
					$('.order-container').append('<div id="ss'+data[0]+lot_no+order_qty+'"><div class="d-none"><input type="text" name="product_codes[]" value="'+data[0]+'"><input type="text" name="order_qty[]" value="'+order_qty+'"><input type="text" name="lotno[]" value="'+lotno_res[1]+'"><input type="text" name="location[]" value="'+location+'"></div> <span class="bg-primary text-white px-2"><a type="button" id="remove_btn"><i class="material-icons">close</i></a> '+order_qty+' '+data[5]+' <small>('+lotno_res[1]+')</small></span></div> ');
				}
			});

		}
	});

	$('.order-container').on('click', '#remove_btn', function(e) {
		e.preventDefault();
		var id = $(this).parent().parent().attr('id');
		$('div #'+id).remove();
	})

})


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

		var id = $('#product_codes').val();
		var order_qty = $('#order_qty').val();
		var lot_no = $('#lotno').val();

		if(id == null || order_qty == null || lot_no == null) {
			$.Toast('Please check required field', errorToast);
		}
		else if(order_qty < 0) {
			$.Toast('Invalid Quantity', {
				'width': 0,
				'duration': 4000,
				'position': 'top',
				'align': 'right',
				'zindex': 99999
			});
		} 
		else {
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

		}

	})

}

// function searchValue(str) {
	// if(str.length == 0) {
	// 	$('#product_codes').load('controller/controller.inout.php?mode=getAllProductCode');
	// 	$('#lotno').load('');
	// 	return;
	// }
	// else {
	// 	var xmlhttp = new XMLHttpRequest();
	// 	xmlhttp.onreadystatechange = function() {
	// 		if (this.readyState == 4 && this.status == 200) {
	// 			var responseVal = this.responseText;
	// 			var obj = $.parseJSON(responseVal);

				// $('#product_codes').load('controller/controller.inout.php?mode=getProductCode&product_code='+obj.product_code);

				// $('#product_codes').filter(function() {
				// 	load('controller/controller.inout.php?mode=getProductCode&product_code='+obj.product_code);
				// });

				// $('#lotno').load('controller/controller.addorder.php?mode=getLotnumber&product_id='+obj.product_id);

		// 	}
		// }
		// xmlhttp.open("GET", "controller/controller.inout.php?mode=searchCode&product_code="+str, true);
		// xmlhttp.send();
	// }
// }