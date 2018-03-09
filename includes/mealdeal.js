

var disabledDays = "";
var enabledDays = "";
var offDays = [];


$(document).ready(function () {


	$('#date').datepicker({
    	format: 'yyyy-mm-dd'
	});

	$('#date').prop("disabled", true);

	$('#horizontalTab').easyResponsiveTabs({
		type: 'default', //Types: default, vertical, accordion           
		width: 'auto', //auto or any width like 600px
		fit: true,   // 100% fit in a container
		closed: 'accordion', // Start closed if in accordion view
		activate: function(event) { // Callback function if tab is switched
			var $tab = $(this);
			var $info = $('#tabInfo');
			var $name = $('span', $info);
			$name.text($tab.text());
			$info.show();
		}
	});

})


function getSchoolDays() {
  var student = $( "#student option:selected" ).attr("value");
  if (student != undefined && student != "") {
    	var data = "id=" + student;
		$.ajax({
			 type: "GET",
			 url: webRoot + "ajax/get-school-days-from-student.php",
			 data: data,
			 error: function (xhr, status, error) {
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
			 	disabledDays = "";
				enabledDays = "";
				
				var result = $.parseJSON(data);
				if (result.sun == "0")
				   disabledDays += "0";
				else
				   enabledDays += "0";

				if (result.mon == "0")
				   disabledDays += "1";
				else
				   enabledDays += "1";
				   
				if (result.tue == "0")
				   disabledDays += "2";
				else
				   enabledDays += "2";
				
				if (result.wed == "0")
				   disabledDays += "3";
				else
				   enabledDays += "3";
				
				if (result.thu == "0")
				   disabledDays += "4";
				else
				   enabledDays += "4";

				if (result.fri == "0")
				   disableDays += "5";
				else
				   enabledDays += "5";
				   
				if (result.sat == "0")
				   disabledDays += "6";
				else
				   enabledDays += "6";
				   
				$('#date').datepicker('setDaysOfWeekDisabled', disabledDays);
				$('#date').datepicker('setDaysOfWeekHighlighted', enabledDays);
			    $('#date').datepicker('setStartDate', result.start_date);

				$('#date').prop("disabled", false);


			} // function(data)
		});

  } else {
  	$('#date').prop("disabled", true);
	$( "#student" ).attr("value", "");

  }
}


function addToCart() {
  var qty = $('#qty').val();
  if (parseInt(qty) == 0) {
  	alert("Qty cannot be zero");
	return;
  }
  if (orderInProcess != 1 && orderInSession != 1)
	  $('#orderingslct_popup').modal("show");
  else {
     ovalidate(document.frmO);
  }

  
}
function ovalidate(frm) {
 var retVal = true;
 var student = $( "#student option:selected" ).attr("value");
 var pid = frm.pid.value;
 var qty = $('#qty').val();
 var mealType = $('#mtype').val();
 var price = $('#price').val();
 
 if (student === undefined  || student == "") {
 	$('#divStudent').addClass("error-border");
		$("#divStudent").formError(
			"No student selected ",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
 } else {
 	$('#divStudent').removeClass("error-border");
	$("#divStudent").formError({remove:true,  successImage: {enabled:false}});
 }
  if (retVal) {
    postToCart(pid, student, qty, date, mealType, price);
	
 }
 return false;
}

function postToCart(pid, student, qty, date, mealType, price) {
	var data = "pid=" + pid + "&student=" + student + "&qty=" + qty +"&date=" + date
	   + "&mtype=" + mealType + "&price=" + price
	
	$('#lnkAddToCart').hide();
	$('#imgLoader').show();
	$('#orderingslct_popup').modal("hide");
	
	$.ajax({
			 type: "POST",
			 url: webRoot + "ajax/post-to-cart.php",
			 data: data,
			 error: function (xhr, status, error) {
					$('#lnkAddToCart').show();
					$('#imgLoader').hide();

				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
 					$('#lnkAddToCart').show();
					$('#imgLoader').hide();

			   if (parseInt(data) >= 0) {
			   	  $('#success-modal').modal("show");
			   } else if (data == "INCREMENTED") {
				  $('#increment-modal').modal("show");
			   }
			   else {
			   	  $('#errorMessage').html("<b>" + data + "</b>");
				  $('#error-modal').modal("show");
			   }
			} // function(data)
		});

}

function back() {
 window.location = webRoot + "products-list";
}


