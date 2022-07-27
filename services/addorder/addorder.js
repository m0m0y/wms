$(function(){
	customFileInput();
	ajaxForm();

	$('#product_codes').load('controller/controller.inout.php?mode=getAllProductCode');

	// $('#product_codes').change(function() {
    //     var product_id = $(this).val();
	// 		$('#lotno').load('controller/controller.addorder.php?mode=getLotnumber&product_id='+product_id);
    // })

	// $('#lotno').change(function() {
	// 	var lotno_id = $(this).val();

	// 	$('#location').load('controller/controller.inout.php?mode=getLotnumber&product_id='+lotno_id);
	// })

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

		loader();

		var $inputs = $(this).find("input, select, button, textarea");
		var action = $(this).attr("action");
		var type = $(this).attr("method");
		var formData = new FormData(this);

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

	})

}

function searchValue(str) {
	if(str.length == 0) {
		$('#product_codes').load('controller/controller.inout.php?mode=getAllProductCode');
		$('#lotno').load('');
		return;
	} 
	else {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var responseVal = this.responseText;
				var obj = $.parseJSON(responseVal);

				$('#product_codes').load('controller/controller.inout.php?mode=getProductCode&product_code='+obj.product_code);

				// $('#lotno').load('controller/controller.inout.php?mode=getLotnumber&product_id='+obj.product_id);

			}
		}
		xmlhttp.open("GET", "controller/controller.inout.php?mode=searchCode&product_code="+str, true);
		xmlhttp.send();
	}
}