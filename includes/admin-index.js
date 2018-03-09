$(document).ready(function() {
	

		
 });

function xvalidate() {
	
	var message = "<ul>";
	var retVal = true;

	var frm = document.frmSettings;
	var smtp_server = frm.smtp_server.value;
	var smtp_uid = frm.smtp_uid.vaue;
	var smtp_pwd = frm.smtp_pwd.value;
	
	var sms_api_uid = frm.sms_api_userid.value;
	var sms_api_pwd = frm.sms_api_pwd.value;

	var recess_deliv_time = frm.recess_deliv_time.value;
	var lunch_deliv_time = frm.lunch_deliv_time.value;

	alert(recess_deliv_time + "," + lunch_deliv_time);

	if (username.trim().length < 3) {
		message += "<li>User name looks invalid.</li>";
		retVal = false;
	}

	if (pwd.trim().length < 3) {
		message += "<li>Password should be at least 3 characters</li>";
		retVal = false;
	}

	
	message += "</ul>";
	
	if (!retVal) {
		jQuery('#errorContent').html(message);
		jQuery('#errorLabel').text("Login Errors");
		jQuery('#errors-popup').modal();
		return false;
	}

	return false;

}




