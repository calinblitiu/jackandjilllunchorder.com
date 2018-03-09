
$(document).ready(function () {

		$('#btnCancel').click(function () {
			window.location = "dashboard.php";
		});


	 //  Image upload Handler:
	    var url = "../items/index.php";
	    $('#flImage').fileupload({
	        url: url,
	        dataType: 'json',
			add: function (e, data) {
				var isvalid = true;
				$.each(data.files, function (index, file) {
					isvalid = (/\.(gif|jpg|jpeg|tiff|png)$/i).test(file.name);
			    });
				if (isvalid) {
					$('#progress').show();
					data.submit();
				} else {
					$('#progress .progress-bar').css('width', '0%');
					$('#files').html("");
				}
			},
	        done: function (e, data) {
	            $.each(data.result.files, function (index, file) {
	                $('<p/>').text(file.name).appendTo('#files');
			$('#file_image').attr("value", file.name);
			$('#imgPreview').attr("src", "../items/files/" + file.name);
			$('#imgPreview').show();
	            });
	        },
	        progressall: function (e, data) {
	            var progress = parseInt(data.loaded / data.total * 100, 10);
	            $('#progress .progress-bar').css(
	                'width',
	                progress + '%'
	            );
	        }
		    }).prop('disabled', !$.support.fileInput)
    		    .parent().addClass($.support.fileInput ? undefined : 'disabled');
		

})


function xvalidate() {
	
	var message = "<ul>";
	var retVal = true;

	var frm = document.frmMD;
	var id = frm.xid.value;
	var name = frm.name.value;
	var price = frm.price.value;
	var descrip = frm.descrip.value;
	var ccode = frm.ccode.value;


	if (name.trim().length < 2) {
		message += "<li>Meal Deal needs a name.</li>";
		retVal = false;
	}
	
	if (price == null || price == "" || isNaN(price)) {
		message += "<li>Price is not valid.</li>";
		retVal = false;
	}

	if (ccode == null || ccode == "") {
		message += "<li>Canteen Code is required</li>";
		retVal = false;
	}

	var itemSelected = false;
	$('.dropdown').each(function (item, index) {
		if ($(this).val() != "0") {
			itemSelected = true;
		}
	});

	if (!itemSelected) {
		message += "<li>Please select at least one item</li>";
		retVal = false;
	}

	message += "</ul>";
	
	if (!retVal) {
		jQuery('#errorContent').html(message);
		jQuery('#errorLabel').text("Form Errors");
		jQuery('#errors-popup').modal();
		return false;
	}

	return true;

}




