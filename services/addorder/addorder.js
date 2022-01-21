$(function(){
	customFileInput();
	ajaxForm();
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

}