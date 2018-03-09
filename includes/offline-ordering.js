
$(document).ready(function () {

	if (loginPopupShow == 1) 
		$('#notLoggedInPopup').modal({
            show: true,
            keyboard: false,
            backdrop: 'static'
        });

	if (showNoStudentPopup == 1) 
		$('#noStudentsPopup').modal({
            show: true,
            keyboard: false,
            backdrop: 'static'
        });
		
	if (showNoStudentPopup != 1 && showNoStudentPopup == 1) 
		$('#student_popup').modal({
            show: true,
            keyboard: false,
            backdrop: 'static'
        });
	if (selStudent != 0) {
		doPrint(selStudent);
	}
})

function getStudent() {
 var student = $( "#student option:selected" ).attr("value");
  if (student != undefined && student != "") {
		window.location = webRoot + "offline-ordering/" + student;
  }
}


function doPrint(id) {
		$.ajax({
			 type: "GET",
			 url: webRoot + "ajax/print-school-menu.php",
			 data: "student=" + id,
			 error: function (xhr, status, error) {
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
			   if (data.indexOf("ERROR") == -1) {

			   } else {
			   	alert(data);
			   }
			} // function(data)
		});
	
}

