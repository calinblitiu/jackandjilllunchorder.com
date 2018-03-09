
$(document).ready(function () {
	if (error_message != "")
		$('#error-modal').modal("show");

	$('#lnkForgot').click(function() {
		$('#forgot-modal').modal("show");
	});

	$('#btnForgot').click(function () {
		doForgot();
	});

	$('input').focus(function() {
		$(this).removeClass("error-border");
		$(this).formError({remove:true,  successImage: {enabled:false}});

	});

	if (verify_modal == 1)
		$('#verify-modal').modal("show");

	$('#btnVerify').click(function () {
		doVerify();
	});

	$('#lnkSendAgain').click(function () {
		doNewCode();
	});

});


function xvalidate() {
	var retVal = true;

	var frm = document.frm;
	var email = frm.email.value;
	var pwd = frm.pwd.value;
	

	
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


	if (!retVal) {
		return false;
	}
	return true;

}


function doForgot() {

 var frm = document.frmF;
 var email = frm.femail.value;

 if (email.indexOf("@") == -1 || email.indexOf(".") == -1) {
 	alert("Please enter a valid email id");
	return false;
 }

 $('#imgLoaderF').show();
 $('#btnForgot').hide();

			$.ajax({
			 type: "POST",
			 url: "ajax/do-reset-pwd.php",
			 data: "email=" + email,
			 error: function (xhr, status, error) {
				 $('#imgLoaderF').hide();
				 $('#btnForgot').show();

				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
			 	 $('#imgLoaderF').hide();
					 $('#btnForgot').show();

				if (data.trim() != "SUCCESS")
					alert(data);
				else {
					 alert("A new password has been sent to your mailbox");
					 window.location = "sign-in";
				}

			} // function(data)
		});

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
