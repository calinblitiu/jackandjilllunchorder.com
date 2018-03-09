
$(document).ready(function () {


		$('#lnkAdd').click(function () {
			window.location = "edit-school.php";
		});

		

})

function doPaging(p) {
	document.frmMenu.p.value = p;
	document.frmMenu.submit();
}

function doDel(id) {
  if (confirm("Are you sure?")) {
	window.location = "schools.php?del=" + id;
  }
}


function doPrint(id) {

		$.ajax({
			 type: "POST",
			 url: "../ajax/print-school-menu.php",
			 data: "school=" + id,
			 error: function (xhr, status, error) {
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
			   if (!isNaN(data)) {
			     $('#lnkPrint').attr("href", webRoot + "output/school-" + id + ".pdf");
				 $('#print-modal').modal("show");

			   } else {
			   	alert(data);
			   }
			} // function(data)
		});
	
}

