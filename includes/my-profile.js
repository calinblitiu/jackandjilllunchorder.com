
$(document).ready(function () {

	$('input').focus(function() {
		$(this).removeClass("error-border");
		$(this).formError({remove:true,  successImage: {enabled:false}});

		$("input").removeClass("error-border");
		$('input').formError({remove:true,  successImage: {enabled:false}});

	});


//  Image upload Handler:
	    var url = "profiles/index.php";
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
			$('#imgPreview').attr("src", "profiles/files/" + file.name);
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
		

	if (error_message != "") {
		$('#error-modal').modal("show");
	}

	if (success_message != "") {
		$('#success-modal').modal("show");
	}

})


function xvalidate() {
	var retVal = true;

	var frm = document.frm;
	var fname = frm.fname.value;
	var lname = frm.lname.value;
	var mobile = frm.mobile.value;
	var email = frm.email.value;
	
	if (fname.trim().length < 3) {
		$('#fname').addClass("error-border");
		$("#fname").formError(
			"First Name cannot be less than 3 characters",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#fname').removeClass("error-border");
		$("#fname").formError({remove:true,  successImage: {enabled:false}});

	}

	if (lname.trim().length < 3) {
		$('#lname').addClass("error-border");
		$("#lname").formError(
			"Last Name cannot be less than 3 characters",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#lname').removeClass("error-border");
		$("#lname").formError({remove:true,  successImage: {enabled:false}});

	}

	if (mobile.trim().length < 12) {
		$('#mobile').addClass("error-border");
		$("#mobile").formError(
			"Only 12 digits with country code prefix ",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#mobile').removeClass("error-border");
		$("#mobile").formError({remove:true,  successImage: {enabled:false}});

	}

	
	if (email.indexOf("@") == -1 || email.indexOf(".") == -1) {
		$('#email').addClass("error-border");
		$("#email").formError(
			"Email id looks invalid ",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#email').removeClass("error-border");
		$("#email").formError({remove:true,  successImage: {enabled:false}});
		
	}



	if (!retVal) {
		return false;
	}

	return true;

}



