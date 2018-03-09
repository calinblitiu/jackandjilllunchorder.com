
var selItemCount = 0;
var subsTotal = 0;

var fStudent= 0;
var fMealDealQty = 0;
var fMealDealPrice = 0;
var fMealDealAmount = 0;
var fDays = "";
var fPayType = "";
var fReminder7am = 0;
var fReminder7pm = 0;

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

	$('#ckMealDeal').change(function() {
		if ($(this).prop("checked")) {
			$('#lnkMealDeal').show();
		} else {
			$('#lnkMealDeal').hide();
		}
	});
	$('#ckSelItems').change(function() {
		if ($(this).prop("checked")) {
			$('#lnkSelItems').show();
		} else {
			$('#lnkSelItems').hide();
		}
	});
	
	$('#lnkMealDeal').click(function () {
	 	var student = $( "#student option:selected" ).attr("value");
		if (student != undefined && student != "") {
			$('#mealDealPopup').modal("show");
		}
		else 
			alert("Please select a student first");
		return false;
	});

	$('#lnkUpdateMD').click(function () {
		validateMD();
		return false;
	});
	
	$('#lnkSelItems').click(function () {
	    var student = $( "#student option:selected" ).attr("value");
		if (student != undefined && student != "") {
			getSubsItems(true);
		}
		else 
			alert("Please select a student first");
		return false;
	});
	
	$('#btnAddSubs').click(function () {
		getSchoolItems();
		$('#subsPopup').modal("hide");
		// Show the next modal after the fade effect is finished
		setTimeout(function(){ $('#itemPopup').modal('show'); }, 500);
	});

	$('#btnSubmit').click(function () {
		if (confirm("Proceed with subscription?"))
		xvalidate();
	});

	var student = $( "#student option:selected" ).attr("value");
	if (student != undefined && student != "") {
	    fStudent = student;
		getSubsItems(false);
		getSchoolWeekDays();


	}

	if (success == "1") {
		$('#success-modal').modal("show");
	}
	

})


function getSchoolWeekDays() {
  var student = $( "#student option:selected" ).attr("value");
  fStudent = student;
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

				var html = "<h4>School Days</h4>";
				if (result.sun == "0")
				   disabledDays += "0";
				else {
				   html += addToWeekDays(0);
				   enabledDays += "0";
				}
				if (result.mon == "0")
				   disabledDays += "1";
				else {
				   html += addToWeekDays(1);
				   enabledDays += "1";
				}   
				if (result.tue == "0")
				   disabledDays += "2";
				else {
				   html += addToWeekDays(2);
				   enabledDays += "2";
				}
				if (result.wed == "0")
				   disabledDays += "3";
				else {
				   html += addToWeekDays(3);
				   enabledDays += "3";
				}
				if (result.thu == "0")
				   disabledDays += "4";
				else {
				   html += addToWeekDays(4);
				   enabledDays += "4";
				}
				if (result.fri == "0")
				   disabledDays += "5";
				else {
	  			   html += addToWeekDays(5);
				   enabledDays += "5";
				}   
				if (result.sat == "0")
				   disabledDays += "6";
				else {
	  			   html += addToWeekDays(6);
				   enabledDays += "6";
				}   
				
				$('#divWeekDays').html(html);
				ovalidate(document.frmSubs);
			} // function(data)
		});

  } else {

  }
}

function addToWeekDays(day) {
	var days = new Array();
	days.push("Sunday");
	days.push("Monday");
	days.push("Tuesday");
	days.push("Wednesday");
	days.push("Thursday");
	days.push("Friday");
	days.push("Saturday");

	var html = "<div class=\"checkbox\">";
	html += "<label>";
	html += "<input type=\"checkbox\" value=\"" + day + "\" class='daychecks'>";
	html += "<span class=\"cr\"><i class=\"cr-icon glyphicon  glyphicon-ok\"></i></span>";
	html += days[day];
	html += "</label>";
	html += "</div>";

	return html;

}


function ovalidate(frm) {
 var retVal = true;
 
 var student = $( "#student option:selected" ).attr("value");

 
 if (student === undefined  ) {
		return false;
 } else {
 }


 if (retVal) {
    checkSubsStatusForStudent(student);
	
 }
 return false;
}

function checkSubsStatusForStudent(student) {
	var data = "student=" + student;
	$('#student_popup').modal("hide");
	
	$.ajax({
			 type: "POST",
			 url: webRoot + "ajax/check-subs-status-for-student.php",
			 data: data,
			 error: function (xhr, status, error) {

				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){

			   if (data == "") {
			   }
			   else {
			   	  alert(data);
			   }
			} // function(data)
		});

}



function validateMD() {
	if (parseInt($('#mdqty').val()) < 1) {
		alert("Qty should be at least 1");
		return;
	}

	$('#mealDealPopup').modal("hide");
}

function setCategory(id) {
	$('.catbtn').removeClass("btnCatSelected");
	$('#btnCat' + id).addClass("btnCatSelected");
	document.frmItem.itemcat.value = id;
	getSchoolItems();
}


function getSchoolItems() {
  var student = $( "#student option:selected" ).attr("value");
  var mealType = "R";
  if ($('#mealTypeL').prop("checked"))
    mealType = "L";
  if ($('#mealTypeRL').prop("checked"))
    mealType = "RL";
  var cat = document.frmItem.itemcat.value;
  
  //var mealType =  
  if (student != undefined && student != "") {
    	var data = "id=" + student + "&mt=" + mealType + "&cat=" + cat;
		$.ajax({
			 type: "GET",
			 url: webRoot + "ajax/get-school-items-for-subs.php",
			 data: data,
			 error: function (xhr, status, error) {
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
  				var result = $.parseJSON(data);
				if (result != null) {
				    var count = result.length;
				    var html = "";
  			        for (var i = 0; i < count; i ++) {
				        var entry = result[i];
						html += "<tr>";
						html += "<td class=\"col-sm-3\"><a href=# onclick=\"selItem(" + entry.ID + ",'" + entry.price + "'); return false;\"><img src=\"" + webRoot + "items/files/" + entry.image + "\" class=\"img img-responsive\"</a></td>";
						html += "<td class=\"col-sm-5 text-left\"><a href=# onclick=\"selItem(" + entry.ID + ", '" + entry.price + "'); return false;\">" + entry.name + "</a></td>";
						html += "<td class=\"col-sm-5 text-right\">$" + entry.price + "</td>";

						html += "</tr>";
				     } 
				     $("#tblSelItems > tbody").html("");
				     $("#tblSelItems tbody").append(html);
	
				
			 	}
			 } // function(data)
		});

  } else {
		alert("Please choose a student first");
		return false;
  }
}

function selItem(item_id, price) {
  var data = "pid=" + item_id + "&qty=1";
  var mealType = "R";
  if ($('#mealTypeL').prop("checked"))
    mealType = "L";
  if ($('#mealTypeRL').prop("checked"))
    mealType = "RL";
  var student = $( "#student option:selected" ).attr("value");	
  data += "&mtype=" + mealType + "&price=" + price + "&student=" + student;
  
  $('#itemPopup').modal("hide");
	$.ajax({
			 type: "POST",
			 url: webRoot + "ajax/post-to-subs.php",
			 data: data,
			 error: function (xhr, status, error) {

				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){

			   if (data == "" || data == "INCREMENTED") {
			   	// Show the next modal after the fade effect is finished
				setTimeout(function(){ getSubsItems(true); }, 500);
			   }
			   else {
			   	  alert(data);
			   }
			} // function(data)
		});
	
	
}


function getSubsItems(show) {
 var student = $( "#student option:selected" ).attr("value");	
  if (student != undefined && student != "") {
		$.ajax({
			 type: "GET",
			 url: webRoot + "ajax/get-subs-items.php",
			 data: "",
			 error: function (xhr, status, error) {
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
  				var result = $.parseJSON(data);
				if (result == null) {
					alert("Error getting subscription items");
				}
				var count = result.length;
				selItemCount = count;
				$('#subsTotal').html("Subscription Items (" + count + ")");
				
				var html = "";
				var total = 0;
  			    for (var i = 0; i < count; i ++) {
				   var entry = result[i];
				   if (entry.meal_type == "R")
				   	 entry.meal_type = "Recess";
				   else if (entry.meal_type == "L")
				     entry.meal_type = "Lunch";
				   else if (entry.meal_type == "RL")
				     entry.meal_type = "Recess+Lunch";
				
				   total += parseFloat(entry.qty) * parseFloat(entry.price);
				   
				   
				    html += "<tr>";
					html += "<td class=\"text-left\"><img src=\"" + webRoot + "items/files/" + entry.image + "\" class=\"img img-responsive\" align=top>"+ entry.productname+ "</td>";
					html += "<td>" + entry.meal_type + "</td>";
					html += "<td  class=\"text-right\"><input class=\"form-control sqtyfield\" type=number min=1 max=20 value=" + entry.qty + " id='sqty" + entry.ID + "' ></td>";
					html += "<td  class=\"text-right\">$" + parseFloat(entry.price).toFixed(2) + "</td>";
					html += "<td  class=\"text-right\">$" + (parseFloat(entry.qty) * parseFloat(entry.price)).toFixed(2) + "</td>";
					html += "<td class=\"text-right\"><a href=# onclick=\"delSubs(" + entry.product_id +"); return false;\"><i class='fa fa-close fa-2x fg-red'></i></a>";
					html += "</td>";
					html += "</tr>";

				} 
  					html += "<tr>";
					html += "<td colspan=3></td>";
					html += "<td class=\"text-right\"><b>Total</b></td>";
					html += "<td class=\"text-right\"><b>" +  (parseFloat(total)).toFixed(2) + "</b></td>";
					html += "</tr>";
					 
				     $("#tblSubs> tbody").html("");
				     $("#tblSubs tbody").append(html);
					subsTotal = total;
				
					$('.sqtyfield').change(function() {
							var thisId = $(this).attr("id");
							var thisVal = $(this).val();
							thisId = thisId.replace(/sqty/, "");
							updateSubsQty(thisId, thisVal);
							
							});
				
					if (show)
						$('#subsPopup').modal("show");	
					else
						$('#subsPopup').modal("hide");	
					

			 } // function(data)
		});

  } else {
		alert("Please choose a student first");
		return false;
  }
}

function delSubs(item_id) {
	if (!confirm("Are you sure?"))
	 return false;
	 
	var data = "pid=" + item_id;
	$.ajax({
			 type: "POST",
			 url: webRoot + "ajax/del-subs-item.php",
			 data: data,
			 error: function (xhr, status, error) {

				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){

			   if (data == "") {
			   	  getSubsItems(true);
			   }
			   else {
			   	  alert(data);
			   }
			} // function(data)
		});

}

function updateSubsQty(id, qty) {
	var data = "id=" + id + "&qty=" + qty;
	$.ajax({
			 type: "POST",
			 url: webRoot + "ajax/update-subs-item.php",
			 data: data,
			 error: function (xhr, status, error) {

				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){

			   if (data == "") {
			   	  getSubsItems(true);
			   }
			   else {
			   	  alert(data);
			   }
			} // function(data)
		});

}

function xvalidate() {

  var student = $( "#student option:selected" ).attr("value");
  if (student = undefined || student == "") { 
  	alert("No student has been selected");
	return false;
  }
  	
  
  if ($('#ckMealDeal').prop("checked") == false && $('#ckSelItems').prop("checked") == false) {
  	alert("You have not checked any menu items");
	return false;
  }
  if ($('#ckSelItems').prop("checked") != false) {
	  if (parseInt(selItemCount) < 1) {
	  	alert("No menu items have been selected");
		return false;
	  }
  }
  fMealDealQty = 0;
  if ($('#ckMealDeal').prop("checked")) {
  	 fMealDealQty = $('#mdqty').val();
	 fMealDealPrice = $('#mealDealPrice').val();
	 fMealDealAmount = parseFloat(fMealDealQty) * parseFloat(fMealDealPrice);
  }
  fDays = "";
  $('.daychecks').each(function(i, obj) {
  	 if ($(this).prop("checked"))
	   fDays += $(this).val() +",";
  });
  if (fDays == '' ) {
  	alert("No weekdays have been chosen");
	return false;
  }
  if ($('#ckReminder7am').prop("checked"))
    fReminder7am = 1;
  else
    fReminder7am = 0;

  if ($('#ckReminder7pm').prop("checked"))
    fReminder7pm = 1;
  else
    fReminder7pm = 0;

  document.frmCredit.student.value = fStudent;
  if ($('#payCash').prop("checked")) {
     $('#btnSubmit').hide();
	 $('#imgLoader').show();
	 
  } else if ($('#payEway').prop("checked")) {
  	    $('#paytype').val("EWAY");

  		var arrDays = fDays.split(',');
		var dayCount = arrDays.length-1;
		if (dayCount < 0)
			dayCount = 0;
		var amt = parseFloat(subsTotal) + parseFloat(fMealDealAmount);
		amt = amt * dayCount;
		
  	    fPayType = "EWAY";
	  	$('#amount').val(parseFloat(amt).toFixed(2));
	  	$('#amount2').val(parseFloat(amt).toFixed(2));

	  	if (tokenId == null || tokenId == '') { 
			$('input').formError({remove:true,  successImage: {enabled:false}});
			$('#dummy-popup').modal("show");
		} else {
			$('input').formError({remove:true,  successImage: {enabled:false}});
			$('#token-popup').modal("show");
		
		}

  
  } else if ($('#payWallet').prop("checked")) {
  	fPayType = "WALLET";
 	$('#paytype').val("WALLET");


	$('#btnSubmit').hide();
	$('#imgLoader').show();
	
	var arrDays = fDays.split(',');
	var dayCount = arrDays.length-1;
	if (dayCount < 0)
			dayCount = 0;

	var amt = parseFloat(subsTotal) + parseFloat(fMealDealAmount);
	amt = amt * dayCount;

	var data = "student=" + fStudent + "&mdqty=" + fMealDealQty + "&days=" + fDays + 
		"&paytype=" + fPayType + "&reminder7am=" + fReminder7am + "&reminder7pm=" + 
		fReminder7pm + "&amount=" + parseFloat(amt) + "&ajax=1";

   	 $.ajax({
			 type: "POST",
			 url: webRoot + "ajax/add-subscription.php",
			 data: data,
			 error: function (xhr, status, error) {
			   $('#imgLoader').hide();
			   $('#btnSubmit').show();


				 	alert(xhr +"," + status +"," + error);
			 },
			 success: function(data){

		 	   $('#imgLoader').hide();
			   $('#btnSubmit').show();

			   if (data == "") {
				    window.location = webRoot + "subscription-confirmation";

			   } else {
			   	  $('#errorMessage').html("<b>" + data + "</b>");
				  $('#error-modal').modal("show");
			   }
			} // function(data)
		});


  }
  
}

function useCard() {
	$('#token-popup').modal("toggle");
	$('#dummy-popup').modal("show");

}


function cvalidate() {
	var retVal = true;

	var frm = document.frmCredit;
	var amount = frm.amount.value;

	var cc = frm.EWAY_CARDNUMBER.value;
	var mm = frm.mm.value;
	var yyyy = frm.yyyy.value;
	var cvv = frm.EWAY_CARDCVN.value;
	

	if (namec.trim().length < 5) {
		$('#namec').addClass("error-border");
		$("#namec").formError(
			"Name looks invalid",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#namec').removeClass("error-border");
		$("#namec")	.formError({remove:true,  successImage: {enabled:false}});

	}

	if (amount == null || amount == "" || isNaN(amount) || parseInt(amount) < 1 
			) {
		$('#amount').addClass("error-border");
		$("#amount").formError(
			"Amount should be a valid number without decimal places",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#amount').removeClass("error-border");
		$("#amount").formError({remove:true,  successImage: {enabled:false}});

	}

	if (cc.trim().length < 16) {
		$('#EWAY_CARDNUMBER').addClass("error-border");
		$("#EWAY_CARDNUMBER").formError(
			"Card must be 16 characters",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#EWAY_CARDNUMBER').removeClass("error-border");
		$("#EWAY_CARDNUMBER").formError({remove:true,  successImage: {enabled:false}});

	}

	if (mm == null || mm == "" || isNaN(mm) || parseInt(mm) < 1 || parseInt(mm) > 12) {
		$('#mm').addClass("error-border");
		$("#mm").formError(
			"Month should be between 1 -12",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#mm').removeClass("error-border");
		$("#mm").formError({remove:true,  successImage: {enabled:false}});

	}

	if (yyyy == null || yyyy == "" || isNaN(yyyy) || parseInt(yyyy) < 2018) {
		$('#yyyy').addClass("error-border");
		$("#yyyy").formError(
			"Year should be at least 2018",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#yyyy').removeClass("error-border");
		$("#yyyy").formError({remove:true,  successImage: {enabled:false}});

	}
	if (cvv== null || cvv == "" || isNaN(cvv) || cvv.length != 3) {
		$('#EWAY_CARDCVN').addClass("error-border");
		$("#EWAY_CARDCVN").formError(
			"CVV should be 3 digits",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#EWAY_CARDCVN').removeClass("error-border");
		$("#EWAY_CARDCVN").formError({remove:true,  successImage: {enabled:false}});

	}

	if (!retVal) {
		return false;
	}
	return true;
	$('#imgLoaderCC').show();
	$('#btnPay').hide();
	


	var data = "student=" + fStudent + "&mdqty=" + fMealDealQty + "&days=" + fDays + 
		"&paytype=" + fPayType + "&reminder7am=" + fReminder7am + "&reminder7pm=" + 
		fReminder7pm + "&amount=" + amount + "&cc=" + cc + "&mm=" + mm + "&yyyy=" + yyyy 
		+ "&cvv=" + cvv + "&namec=" + namec;
	
	    $.ajax({
			 type: "POST",
			 url: webRoot + "ajax/add-subscription.php",
			 data: data,
			 error: function (xhr, status, error) {
			 	$('#imgLoaderCC').hide();
				$('#btnPay').show();

				 	alert(xhr +"," + status +"," + error);
			 },
			 success: function(data){
			   $('#btnPay').show();
			   $('#imgLoaderCC').hide();
			   if (data == "") {
			   	  window.location = webRoot + "subscription-confirmation";

			   } else {
			   	  $('#errorMessage').html("<b>" + data + "</b>");
				  $('#error-modal').modal("show");
			   }
			} // function(data)
		});

	return false;

}


function tvalidate() {
	var retVal = true;

	var frm = document.frmToken;
	var amount = frm.amount2.value;
	if (amount == null || amount == "" || isNaN(amount) || parseInt(amount) < 1
			) {
		$('#amount2').addClass("error-border");
		$("#amount2").formError(
			"Amount should be a valid number without decimal places",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#amount2').removeClass("error-border");
		$("#amount2").formError({remove:true,  successImage: {enabled:false}});

	}

	if (!retVal) {
		return false;
	}
	$('#imgLoader2').show();
    $('#btnPay2').hide();

	var data = "student=" + fStudent + "&mdqty=" + fMealDealQty + "&days=" + fDays + 
		"&paytype=" + fPayType + "&reminder7am=" + fReminder7am + "&reminder7pm=" + 
		fReminder7pm + "&amount=" + amount + "&ajax=1";
	
	    $.ajax({
			 type: "POST",
			 url: webRoot + "ajax/add-subscription.php",
			 data: data,
			 error: function (xhr, status, error) {
			 	$('#imgLoader2').hide();
    			$('#btnPay2').show();

				 	alert(xhr +"," + status +"," + error);
			 },
			 success: function(data){

		 	   $('#imgLoader2').hide();
    		   $('#btnPay2').show();

			   if (data == "") {
				    window.location = webRoot + "subscription-confirmation";

			   } else {
			   	  $('#errorMessage').html("<b>" + data + "</b>");
				  $('#error-modal').modal("show");
			   }
			} // function(data)
		});

	return false;

}


