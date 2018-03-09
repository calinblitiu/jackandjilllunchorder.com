$(document).ready(function() {
	
	$('#date').datepicker({
    	format: 'yyyy-mm-dd'
	});

	$('.editDateLink').click(function() {
		var thisId = $(this).attr("id");
		thisId = thisId.replace(/date/, "");
		var thisDate = $(this).attr("value");

		var thisStudent = $('#student' + thisId);
		var studentId = thisStudent.attr("value");

		document.frmO.oid.value = thisId;
		document.frmO.date.value = thisDate;
		document.frmO.student.value = studentId;
		
		$( "#student option:selected" ).attr("value", studentId);
		$('#student ').val(studentId).niceSelect('update');
		
		getSchoolDays();		
		
		return false;
	});
	
	$('.editStudentLink').click(function() {
		var thisId = $(this).attr("id");
		thisId = thisId.replace(/student/, "");
		var thisStudent = $(this).attr("value");

		var thisDate = $('#date' + thisId);
		var dateValue = thisDate.attr("value");

		document.frmO.oid.value = thisId;
		document.frmO.date.value = dateValue;
		document.frmO.student.value = thisStudent;
		
		$( "#student option:selected" ).attr("value", thisStudent);
		$('#student ').val(thisStudent).niceSelect('update');
		
		getSchoolDays();		
		
		return false;
	});
	
	

	$('.editMealTypeLink').click(function() {
		var thisId = $(this).attr("id");
		thisId = thisId.replace(/mealType/, "");
		document.frmMT.oid.value = thisId;
		var thisMT = $(this).attr("value");
		
			
	    if (thisMT == "R")
		   document.frmMT.selector[0].checked = true;
	    else if (thisMT == "L")
		   document.frmMT.selector[1].checked = true;
		else if (thisMT == "RL")
		   document.frmMT.selector[2].checked = true;
	

		$('#mealType_popup').modal("show");
		return false;

	});
	
	$('.editNotesLink').click(function() {
		var thisId = $(this).attr("id");
		thisId = thisId.replace(/notes/, "");
		var thisNotes = $(this).attr("value");
		document.frmNotes.oid.value = thisId;
		document.frmNotes.notes.value = thisNotes;
		
		$('#notes_popup').modal("show");
		return false;
		
	});

	
 });


function doPaging(p) {
	document.frmList.p.value = p;
	var link = "/" + p;
	window.location = webRoot + "orders" + link;
}

function viewDetails(id) {

	$.ajax({
			 type: "GET",
			 url: webRoot + "ajax/get-order-details.php",
			 data: "id=" + id,
			 error: function (xhr, status, error) {
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
			   var json = $.parseJSON(data);
			   if (json.result == "ERROR") {
			   	alert(json.message);
				return;
			   }

			   var order = json.order;
			   var orderItems = json.order_items;
			   
			   $('#divXOrderNo').text(order.ID);
			   $('#divXStudent').html(order.student_name + "<Br>Class: " + order.class_name + "<br>" + order.school_name);
			   $('#divXStatus').text(order.status);
			   $('#divXItemCount').text(orderItems.length);
			   $('#divXDate').text(order.nice_order_date);

			   var html = "";
			   for(var i = 0; i < orderItems.length; i++) {
			    	html +="<li>";
					html +="<div class=\"popordr_img\"><img src=\"" + webRoot + "items/files/" + orderItems[i].image + "\"></div>";     
					html +="<div class=\"poporders-dtls\">";
        			html +="<h3>" + orderItems[i].productname + "<br><small>Qty: " + orderItems[i].item_qty + " @ $" + orderItems[i].item_price + "</small> </h3>";
					html += "<h5>For " + orderItems[i].meal_type_string + "</h5> ";
					html += "<div class=\"pop_price\"><h4>$" + (parseFloat(orderItems[i].item_price) * parseFloat(orderItems[i].item_qty)).toFixed(2) + "</h4></div> ";
					html += "</div>";
					html += "</li>";
			   }
			   $('#ul_items').empty();
			   $('#ul_items').html(html);

			   $('#ordr_vdtels').modal("show");
			   
			   
			} // function(data)
		});

 
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

				$('#date_popup').modal("show");

			} // function(data)
		});

  } else {
  	$('#date').prop("disabled", true);
	$( "#student" ).attr("value", "");

  }
}

function ovalidate(frm) {
 var retVal = true;
 
 var student = $( "#student option:selected" ).attr("value");
 var date = frm.date.value;
 var oid = frm.oid.value;
 
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


 if (retVal) {
    updateDateAndStudent(oid, student, date);
	
 }
 return false;
}

function updateDateAndStudent(oid, student, date) {
	var data = "oid=" + oid + "&student=" + student +"&date=" + date;
	
	$('#date_popup').modal("hide");
	
	$.ajax({
			 type: "POST",
			 url: webRoot + "ajax/update-order-date-student.php",
			 data: data,
			 error: function (xhr, status, error) {
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
			   if (data == "") {
			   	  $('#success-modal').modal("show");
			   } else {
			   	alert(data);
			   }
			} // function(data)
		});

}


function mvalidate(frm) {
 var retVal = true;
 
 var oid = frm.oid.value;
 var mealType = frm.selector.value;

 if (retVal) {
    updateMealType(oid, mealType);
	
 }
 return false;
}

function updateMealType(oid, mealType) {
	var data = "oid=" + oid + "&mt=" + mealType;
	
	$('#mealType_popup').modal("hide");
	
	$.ajax({
			 type: "POST",
			 url: webRoot + "ajax/update-order-mealtype.php",
			 data: data,
			 error: function (xhr, status, error) {
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
			   if (data == "") {
			   	  $('#success-modal').modal("show");
			   } else {
			   	alert(data);
			   }
			} // function(data)
		});

}

function saveNotes(oid, mealType) {
	var oid = document.frmNotes.oid.value;
	var notes = document.frmNotes.notes.value;
	
	var data = "oid=" + oid + "&notes=" + notes;
	$('#notes_popup').modal("hide");
	$.ajax({
			 type: "POST",
			 url: webRoot + "ajax/update-order-notes.php",
			 data: data,
			 error: function (xhr, status, error) {
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
			   if (data == "") {
			   	  $('#success-modal').modal("show");
			   } else {
			   	alert(data);
			   }
			} // function(data)
		});

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
