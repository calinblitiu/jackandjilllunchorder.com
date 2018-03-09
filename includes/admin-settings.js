
$(document).ready(function () {

	$('#btnCancel').click(function () {
		window.location="dashboard.php";
	});
})
function xvalidate() {
	
	var message = "<ul>";
	var retVal = true;

	var frm = document.frmSettings;
	var smtp_server = frm.smtp_server.value;
	var smtp_uid = frm.smtp_uid.vaue;
	var smtp_pwd = frm.smtp_pwd.value;
	
	var sms_api_uid = frm.sms_uid.value;
	var sms_api_pwd = frm.sms_pwd.value;

	var recess_deliv_time = frm.recess_deliv_time.value;
	var lunch_deliv_time = frm.lunch_deliv_time.value;

	if (recess_deliv_time == "") {
		message += "<li>Recess delivery time is required.</li>";
		retVal = false;
	}

	if (lunch_deliv_time == "") {
		message += "<li>Lunch delivery time is required.</li>";
		retVal = false;
	}
	if (retVal) {
		var rtime = recess_deliv_time.replace(/:/, "");
		var ltime = lunch_deliv_time.replace(/:/, "");
		if (parseInt(rtime) > parseInt(ltime)) {
			message += "<li>Lunch Delivery time cannot be less than Recess Delivery time</li>";
			retVal = false;
		} 

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


