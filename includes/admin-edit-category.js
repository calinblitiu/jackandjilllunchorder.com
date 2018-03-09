
$(document).ready(function () {

		$('#btnCancel').click(function () {
			window.location = "categories.php";
		});

		 $('#products').multiselect();
})


function xvalidate() {
	
	var message = "<ul>";
	var retVal = true;

	var frm = document.frmCat;
	var id = frm.xid.value;
	var name = frm.name.value;
	var products = frm.products.value;
	
	if (name.trim().length < 5) {
		message += "<li>Category should be at least 5 characters.</li>";
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




