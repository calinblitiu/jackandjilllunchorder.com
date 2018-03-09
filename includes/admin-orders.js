
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

	$('#ckAll').click(function() {
		$('.checkmulti').each(function(index, elem) {
			$(this).prop("checked", $('#ckAll').prop("checked"));
		})
	});		

	$('#btnReset').click(function () {
		$('#order_id').val("");
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

function doDel(id) {
  if (confirm("Are you sure?")) {
	window.location = "xchools.php?del=" + id;
  }
}

function doPrint(id, invoice) {
		$.ajax({
			 type: "POST",
			 url: webRoot + "ajax/print-order.php",
			 data: "id=" + id,
			 error: function (xhr, status, error) {
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
			   if (data == "") {
			     $('#lnkPrint').attr("href", webRoot + "output/" + invoice + ".pdf");
				 $('#print-modal').modal("show");

			   } else {
			   	alert(data);
			   }
			} // function(data)
		});
	
}

function doSlips(id, invoice) {
		$.ajax({
			 type: "GET",
			 url: webRoot + "ajax/print-slips-for-a-date.php",
			 data: "order_id=" + id,
			 error: function (xhr, status, error) {
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
			    $('#lnkPrintSlips').attr("href", webRoot + "output/" + id + "-slips.pdf");
				$('#printslips-modal').modal("show");

			} // function(data)
		});

	
}

function changeStatus(id, xstatus) {
	if (confirm("Are you sure?")) {
		showLoader();
		var data = "order_id=" + id + "&status=" + xstatus;
		$.ajax({
			 type: "POST",
			 url: webRoot + "ajax/change-order-status.php",
			 data: data,
			 error: function (xhr, status, error) {
			 	hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
			 	hideLoader();
				if (data == "") {
					$('#btnReset').trigger("click");
					document.frmMenu.submit();
				}	
				else
					alert(data);

			} // function(data)
		});

		
	}
}

function changeStatusAll(xstatus) {
	var ids = "";
	$('.checkmulti').each(function(index, elem) {
		if ($(this).prop("checked"))
			ids += $(this).val() + ",";
	});
	if (ids == "") {
		alert("No orders selected");
		return;
	}

	if (confirm("Are you sure?")) {
		showLoader();
		var data = "ids=" + ids + "&status=" + xstatus;
		$.ajax({
			 type: "POST",
			 url: webRoot + "ajax/change-all-order-status.php",
			 data: data,
			 error: function (xhr, status, error) {
			 	hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
			 	hideLoader();
				if (data == "")
					window.location.reload();
				else
					alert(data);

			} // function(data)
		});

		
	}
}
