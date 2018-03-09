
$(document).ready(function () {

		$('#btnCancel').click(function () {
			window.location = "allergy-master.php";
		});


	
})


function xvalidate() {
	
	var message = "<ul>";
	var retVal = true;

	var frm = document.frmAllergy;
	var id = frm.xid.value;
	var name = frm.name.value;
	var enabled = 0;
	if ($('#enabled').prop("checked"))
		enabled = 1;
	
	
	if (name.trim().length < 3) {
		message += "<li>Allergy name cannot be less than 3 characters.</li>";
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



