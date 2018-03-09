
$(document).ready(function () {

	$('#date_from').datepicker({
    	format: 'yyyy-mm-dd'
	});

	$('#date_till').datepicker({
    	format: 'yyyy-mm-dd'
	});


	$('#lnkAdd').change(function () {
		window.location = "edit-school.php";
	});
	$('#btnReset').click(function () {
		$('#subs_id').val("");
		$('#date_from').val("");
		$('#date_till').val("");
		$('#status').val("");

	});

	

})

function xvalidate(frm) {

   frm.p.value = 1;

   return true;
}

function doPaging(p) {
	document.frmMenu.p.value = p;
	document.frmMenu.submit();
}


