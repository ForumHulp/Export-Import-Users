; (function ($, window, document) {
	// do stuff here and use $, window and document safely
	// https://www.phpbb.com/community/viewtopic.php?p=13589106#p13589106
	
	$("a.simpledialog").simpleDialog({
	    opacity: 0.1,
	    width: '650px',
		closeLabel: '&times;'
	});

	$('.inputfile').each( function()
	{
		var $input	 = $(this),
			$label	 = $input.next('label'),
			labelVal = $label.html();

		$input.on('change', function(e)
		{
			var fileName = '';
			if (this.files && this.files.length > 0)
			{
				fileName = this.getAttribute('data-multiple-caption').replace('count', this.files.length);
				$label.find('span').html(fileName);
		
				var formData = new FormData($(this).parents('form')[0]);
				formData.append('action', 'add_file');
				formData.append('submit', true);
	
				$.ajax({
					url: '',
					type: 'POST',
					context: document.getElementById("user_import"),
					xhr: function() {
						var myXhr = $.ajaxSettings.xhr();
						return myXhr;
					},
					success: function (result) {
						$(this).html(result);
					},
					data: formData,
					cache: false,
					contentType: false,
					processData: false
				});
			}
			return false;
		});

		// Firefox bug fix
		$input
		.on( 'focus', function(){ $input.addClass( 'has-focus' ); })
		.on( 'blur', function(){ $input.removeClass( 'has-focus' ); });
	});

	$(document).on("click", "#clear_history", function (e) {
		e.preventDefault();
		var form_data = new FormData();
		form_data.append("action", "del_history"); 
		$.ajax({
			type: "POST",
			context: document.getElementById("user_import"),
			url: '', 
			success: function (result) {
				 $(this).html(result);
			},
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,
		});
	});

})(jQuery, window, document);