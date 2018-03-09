
$(document).ready(function () {


		$('#lnkAdd').click(function () {
			window.location = "edit-allergy.php";
		});

		

})

function doPaging(p) {
	document.frmMenu.p.value = p;
	document.frmMenu.submit();
}

function doDel(id) {
  if (confirm("Are you sure?")) {
	window.location = "allergy-master.php?del=" + id;
  }
}

function toggle(id, flag) {
		var data = "id=" + id + "&flag=" + flag;
			$.ajax({
			 type: "GET",
			 url: "../ajax/toggle-allergy-item.php",
			 data: data,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				if (data != "")
					alert(data);
				else {
				  if (flag == 1) {
				  	$('#toggle' + id).html("<i class=\"fa fa-check-square fg-green\"></i>");
					$("#toggle" + id).attr("onclick","toggle(" + id + ",0); return false;");
				  } else {
				  	$('#toggle' + id).html("<i class=\"fa fa-minus-square fg-red\"></i>");
					$("#toggle" + id).attr("onclick","toggle(" + id + ",1); return false;");


				  }
				}

			} // function(data)
		});

  return false;
}
