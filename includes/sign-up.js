
$(document).ready(function () {

	if (error_message != "") {
		$('#error-modal').modal("show");
	}

	if (success_message != "") {
		$('#verify-modal').modal({
            show: true,
            keyboard: false,
            backdrop: 'static'
        });
	}

	$('#btnVerify').click(function () {
		doVerify();
	});

	$('#lnkSendAgain').click(function () {
		doNewCode();
	});
	
	$('input').focus(function() {
		$(this).removeClass("error-border");
		$(this).formError({remove:true,  successImage: {enabled:false}});

	});


})


function xvalidate() {
	var retVal = true;

	var frm = document.frm;
	var fname = frm.fname.value;
	var lname = frm.lname.value;
	var mobile = frm.mobile.value;
	var email = frm.email.value;
	var pwd = frm.pwd.value;
	var pwd2 = frm.pwd2.value;
	
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


	if (mobile.trim().length < 8) {
		$('#mobile').addClass("error-border");
		$("#mobile").formError(
			"Should be at least 8 digits ",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#mobile').removeClass("error-border");
		$("#mobile").formError({remove:true,  successImage: {enabled:false}});

	}

	if (mobile.trim().startsWith("614")) {
		$('#mobile').addClass("error-border");
		$("#mobile").formError(
			"Please do not add the prefix of 614 - only your actual phone number. ",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#mobile').removeClass("error-border");
		$("#mobile").formError({remove:true,  successImage: {enabled:false}});

	}

	if (mobile.trim().startsWith("04")) {
		$('#mobile').addClass("error-border");
		$("#mobile").formError(
			"Please do not add the prefix of 04 - only your actual phone number. ",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#mobile').removeClass("error-border");
		$("#mobile").formError({remove:true,  successImage: {enabled:false}});

	}


	
	if (email.indexOf("@") == -1 || email.indexOf(".") == -1 || email.lastIndexOf(".") < email.indexOf("@")) {
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


	if (pwd.trim().length < 5) {
		$('#pwd').addClass("error-border");
		$("#pwd").formError(
			"Password needs to be at least 5 characters",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#pwd').removeClass("error-border");
		$("#pwd").formError({remove:true,  successImage: {enabled:false}});
		
	}


	if (pwd2 != pwd) {
		$('#pwd2').addClass("error-border");
		$("#pwd2").formError(
			"Passwords do not match ",
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


function doVerify() {

 var frm = document.frmV;
 var code = frm.otp.value;

 if (code == "" || code.trim().length != 4) {
 	$('#divVMessage').text("Please enter the 4 digit code");
	return false;
 }

 $('#imgLoaderV').show();
 $('#btnVerify').hide();
 $('#lnkSendAgain').hide();

			$.ajax({
			 type: "POST",
			 url: "ajax/do-verify.php",
			 data: "otp=" + code,
			 error: function (xhr, status, error) {
				 $('#imgLoaderV').hide();
				 $('#btnVerify').show();
				 $('#lnkSendAgain').show();

				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
			 	 $('#imgLoaderV').hide();
					 $('#btnVerify').show();
					 $('#lnkSendAgain').show();

				if (data.trim() != "SUCCESS")

					$('#divVMessage').text(data);
				else {
					 alert("Your Verification is complete. You can login now");
					 window.location = "sign-in";
				}

			} // function(data)
		});

}

function doNewCode() {

 var frm = document.frmV;
 var email = frm.vemail.value;


 $('#imgLoaderV').show();
 $('#btnVerify').hide();
 $('#lnkSendAgain').hide();

			$.ajax({
			 type: "POST",
			 url: "ajax/generate-new-code.php",
			 data: "email=" + email,
			 error: function (xhr, status, error) {
				 $('#imgLoaderV').hide();
				 $('#btnVerify').show();
				 $('#lnkSendAgain').show();

				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
			 	 $('#imgLoaderV').hide();
				 $('#btnVerify').show();
				 $('#lnkSendAgain').show();

				if (data.indexOf("Error") > -1)
					alert(data);
				else {
					//$('#otp').val(data);
				}

			} // function(data)
		});

}
