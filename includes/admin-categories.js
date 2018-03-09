
$(document).ready(function () {


		$('#lnkAdd').click(function () {
			window.location = "edit-category.php";
		});


})

function doPaging(p) {
	document.frmMenu.p.value = p;
	document.frmMenu.submit();
}

function doDel(id) {
  if (confirm("Are you sure?")) {
	window.location = "categories.php?del=" + id;
  }
}
