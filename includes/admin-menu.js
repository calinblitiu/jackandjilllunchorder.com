
$(document).ready(function () {


		$('#lnkAdd').click(function () {
			window.location = "edit-product.php";
		});

		$('#ftype').change(function () {
			document.frmMenu.submit();
		});

		

})

function doPaging(p) {
	document.frmMenu.p.value = p;
	document.frmMenu.submit();
}

function doDel(id) {
  if (confirm("Are you sure?")) {
	window.location = "menu.php?del=" + id;
  }
}
