
$(document).ready(function () {


		

})

function sortchange() {
  var frm = document.frmMenu;
  if (frm.sort.value == "")
   return;
  else {
  	frm.submit();
  }
}

function xvalidate(frm) {
  frm.p.value = 1;
  return true;
}
function doPaging(p) {
	document.frmMenu.p.value = p;
	document.frmMenu.submit();
}

function doDel(id) {
  if (confirm("Are you sure?")) {
	window.location = "users.php?del=" + id;
  }
}


function showStudents(id) {
		
		showLoader();
			$.ajax({
			 type: "GET",
			 url: "../ajax/get-user-students.php",
			 data: "id=" + id,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				hideLoader();
				var result = $.parseJSON(data);
				var count = result.length;
				$('#studentTitle').html("Students(" + count + ")");
				var html = "<div class=\"students_list\"><div class=\"table-responsive\"> <table class=\"table\">            <thead>            <tr>              <th>	Student Name</th>          <th>	School Name		</th>         <th>Class</th>       <th>Allergies</th>  </tr> </thead><tbody>";
				
			    for(var i = 0; i < count; i ++) {
				     html += "<tr>";
                     html += "<td>" + result[i].name +"</td>";
                     html += "<td>" + result[i].schoolname + "</td>";
                     html += "<td>" + result[i].classname +"</td>";
                     html += "<td>" + result[i].allergies + "</td>";
					 html += "</tr>";
				}	
				
				html += "</tbody></table>";
				$('#divStudents').html(html);
				
				$('#students-popup').modal("show");
				

			} // function(data)
		});
}

function showNotifications(id) {
			
		showLoader();
			$.ajax({
			 type: "GET",
			 url: "../ajax/get-user-details.php",
			 data: "id=" + id,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				hideLoader();
				var result = $.parseJSON(data);
				if (result.notify_neworder_email == 1)
					$('#email_neworder').html("<i class=\"fa fa-check fg-green\"></i>");
				else
					$('#email_neworder').html("<i class=\"fa fa-close fg-red\"></i>");

			    if (result.notify_neworder_sms == 1)
					$('#sms_neworder').html("<i class=\"fa fa-check fg-green\"></i>");
				else
					$('#sms_neworder').html("<i class=\"fa fa-close fg-red\"></i>");

				if (result.notify_status_email == 1)
					$('#email_orderstatus').html("<i class=\"fa fa-check fg-green\"></i>");
				else
					$('#email_orderstatus').html("<i class=\"fa fa-close fg-red\"></i>");

				if (result.notify_status_sms == 1)
					$('#sms_orderstatus').html("<i class=\"fa fa-check fg-green\"></i>");
				else
					$('#sms_orderstatus').html("<i class=\"fa fa-close fg-red\"></i>");

				if (result.notify_newsletter_email == 1)
					$('#email_newsletter').html("<i class=\"fa fa-check fg-green\"></i>");
				else
					$('#email_newsletter').html("<i class=\"fa fa-close fg-red\"></i>");

				if (result.notify_newsletter_sms== 1)
					$('#sms_newsletter').html("<i class=\"fa fa-check fg-green\"></i>");
				else
					$('#sms_newsletter').html("<i class=\"fa fa-close fg-red\"></i>");

				$('#notifications-popup').modal("show");
	

			} // function(data)
		});
}


function doLogin(id) {

	if (confirm("This will end your admin session and log you in as this user. Proceed?")) {
		window.location = "dologin.php?id=" + id;
		
	}
}
function block(id, flag) {
		var data = "id=" + id + "&flag=" + flag;
		showLoader();
		$.ajax({
			 type: "GET",
			 url: "../ajax/block-user.php",
			 data: data,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				hideLoader();
				if (data == "") {
				   if (flag == 1) {
						$('#blockicon' + id).removeClass("fg-red fa fa-lock");
						$('#blockicon' + id).addClass("fg-green fa fa-unlock");
			
						$('#blocklabel' + id).html("Activate");
						$('#btnBlock' + id).attr("onclick", "block(" + id + ", 0)");
				   } else {
					   	$('#blockicon' + id).removeClass("fg-green fa fa-unlock");
						$('#blockicon' + id).addClass("fg-red fa fa-lock");

						$('#blocklabel' + id).html("Deactivate");
						$('#btnBlock' + id).attr("onclick", "block(" + id + ", 1)");

				   }
				} else {
					alert(data);	
				}
				

			} // function(data)
		});

}
