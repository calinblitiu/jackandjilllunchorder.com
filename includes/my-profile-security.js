
$(document).ready(function () {

		
	$('input').focus(function() {
		$(this).removeClass("error-border");
		$(this).formError({remove:true,  successImage: {enabled:false}});

		$("input").removeClass("error-border");
		$('input').formError({remove:true,  successImage: {enabled:false}});

	});

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
	var oldpwd = frm.oldpwd.value;
	var pwd = frm.pwd.value;
	var pwd2 = frm.pwd2.value;
	
	if (oldpwd.trim().length < 6) {
		$('#oldpwd').addClass("error-border");
		$("#oldpwd").formError(
			"Password needs to be at least 6 characters",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#oldpwd').removeClass("error-border");
		$("#oldpwd").formError({remove:true,  successImage: {enabled:false}});

	}

	if (pwd.trim().length < 6) {
		$('#pwd').addClass("error-border");
		$("#pwd").formError(
			"New Password needs to be at least 6 characters",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#pwd').removeClass("error-border");
		$("#pwd").formError({remove:true,  successImage: {enabled:false}});

	}


	if (pwd != pwd2) {
		$('#pwd2').addClass("error-border");
		$("#pwd2").formError(
			"Passwords do not match",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#pwd2').removeClass("error-border");
		$("#pwd2").formError({remove:true,  successImage: {enabled:false}});

	}


	if (!retVal) {
		return false;
	}

	return true;

}



