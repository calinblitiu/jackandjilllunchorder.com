
$(document).ready(function () {

	if (error_message != "") {
		$('#error-modal').modal("show");
	}

	if (success_message != "") {
		$('#success-modal').modal("show");
	}

})


function xvalidate() {
	var retVal = true;

	if (!retVal) {
		return false;
	}

	return true;

}



