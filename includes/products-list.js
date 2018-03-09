
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
		
	if (showNoStudentPopup != 1 && showStudentPopup == 1) 
		$('#student_popup').modal({
            show: true,
            keyboard: false,
            backdrop: 'static'
        });
		
	$('#date').datepicker({
    	format: 'yyyy-mm-dd'
	});

	$('#date').prop("disabled", true);

	if (getSchoolDaysFlag == 1)
		getSchoolDays();
		
})


function doSort() {
  
  xvalidate(document.frmMenu);

}

function xvalidate(frm) {

 var name = frm.xname.value;
 var sort = frm.sort.value;
 if (name == null || name == "")
 	name = "none";
 name = name.replace(/ /, "-");
 window.location = webRoot + "products-list/search/" + name + "/sort/" + sort;
 return false;
}


function showPopup(popup, pid) {
  if (pid != "0")
  	document.frmPopup.pid.value = pid;
	
  if (popup != "")
	  $('#' + popup).modal("show");
  else {
  	pvalidate(document.frmPopup);
  }
}
function pvalidate(frm) {
 var selector = frm.selector.value;
 if (selector == null || selector == "") {
 	alert("Please select meal type");
	return false;
 } else { 
 	window.location = webRoot + "products-list/mt/" + selector;
	return false;

 }
}


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
				offDays = [];
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
				   disabledDays += "5";
				else
				   enabledDays += "5";
				   
				if (result.sat == "0")
				   disabledDays += "6";
				else
				   enabledDays += "6";
				   
				$('#date').datepicker('setDaysOfWeekDisabled', disabledDays);
				$('#date').datepicker('setDaysOfWeekHighlighted', enabledDays);
			    $('#date').datepicker('setStartDate', result.start_date);

				if (result.off_days != null) {
					for(var i=0; i < result.off_days.length; i++) {
						offDays.push(result.off_days[i].date);	
					}
				}
			   $('#date').datepicker('setDatesDisabled', offDays);


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
  if (orderInProcess != 1)
	  $('#orderingslct_popup').modal("show");
  else {
     ovalidate(document.frmO);
  }

  
}

function ovalidate(frm) {
 var retVal = true;
 
 var student = $( "#student option:selected" ).attr("value");
 var date = frm.date.value;
 var mealType = $( "#meal_type option:selected" ).attr("value");

 
 if (student === undefined  ) {
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

 if (date  == null || date == ""  ) {
 	$('#divDate').addClass("error-border");
		$("#divDate").formError(
			"Date is required ",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
 } else {
 	$('#divDate').removeClass("error-border");
	$("#divDate").formError({remove:true,  successImage: {enabled:false}});
 }

 if (mealType  === undefined    ) {
 	$('#divMT').addClass("error-border");
		$("#divMT").formError(
			"Meal Type is required ",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
 } else {
 	$('#divMT').removeClass("error-border");
	$("#divMT").formError({remove:true,  successImage: {enabled:false}});
 }


 if (retVal) {
    addStudentInfoToSession(student, date, mealType);
	
 }
 return false;
}

function addStudentInfoToSession(student, date, mealType) {
	var data = "student=" + student + "&date=" + date + "&meal_type=" + mealType;
	$('#student_popup').modal("hide");
	
	$.ajax({
			 type: "POST",
			 url: webRoot + "ajax/add-student-info-to-session.php",
			 data: data,
			 error: function (xhr, status, error) {

				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){

			   if (data == "") {
				   window.location.reload();
			   }
			   else {
			   	  $('#errorMessage').html("<b>" + data + "</b>");
				  $('#error-modal').modal("show");
			   }
			} // function(data)
		});

}

function doMealType() {
	$('#ordringslct_radiopopup').modal("show");
}

function addToCart(product_id, meal_type, date, price, student) {
 var qty = 1;
 var pid = product_id;
 var mealType = meal_type;

 var data = "pid=" + pid + "&student=" + student + "&qty=" + qty +"&date=" + date
	   + "&mtype=" + mealType + "&price=" + price;
	
	$('#imgLoader').show();
	
	$.ajax({
			 type: "POST",
			 url: webRoot + "ajax/post-to-cart.php",
			 data: data,
			 error: function (xhr, status, error) {
					$('#imgLoader').hide();

				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
					$('#imgLoader').hide();

			   if (parseInt(data) >= 0) {
			   	  $('#hcartcount').html("(" + data + ")");
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

