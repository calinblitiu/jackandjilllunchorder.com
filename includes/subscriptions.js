$(document).ready(function() {
	
	$('#date').datepicker({
    	format: 'yyyy-mm-dd'
	});
	
 });


function doPaging(p) {
	document.frmList.p.value = p;
	var link = "/" + p;
	window.location = webRoot + "subscriptions" + link;
}

function viewDetails(id) {
	$.ajax({
			 type: "GET",
			 url: webRoot + "ajax/get-subscription-details.php",
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

			   var subs = json.subscription;
			   var subsItems = json.subscription_items;
			   
			   $('#divXOrderNo').text(subs.ID);
			   $('#divXStudent').html(subs.student_name + "<Br>Class: " + subs.class_name + "<br>" + subs.school_name);
			   $('#divXItemCount').text(subsItems.length);
			   $('#divXWeekdays').html(subs.weekdays);
			   if (subs.cancel_flag == 1)
			   	 $('#divXStatus').text("Marked for Cancellation");
			   else
			     $('#divXStatus').text("Active");

			   var html = "";
			   for(var i = 0; i < subsItems.length; i++) {
			    	html +="<li>";
					html +="<div class=\"popordr_img\"><img src=\"" + webRoot + "items/files/" + subsItems[i].image + "\"></div>";     
					html +="<div class=\"poporders-dtls\">";
        			html +="<h3>" + subsItems[i].productname + "<br><small>Qty: " + subsItems[i].item_qty + " @ $" + subsItems[i].item_price + "</small> </h3>";
					html += "<h5>For " + subsItems[i].meal_type_string + "</h5> ";
					html += "<div class=\"pop_price\"><h4>$" + (parseFloat(subsItems[i].item_price) * parseFloat(subsItems[i].item_qty)).toFixed(2) + "</h4></div> ";
					html += "</div>";
					html += "</li>";
			   }
			   $('#ul_items').empty();
			   $('#ul_items').html(html);

			   $('#ordr_vdtels').modal("show");
			   
			   
			} // function(data)
		});

 
}


function cancel(id) {


	if (!confirm("Subscription will terminate after next delivery. Are you sure?"))
	  return false;
	  
	$('#lnkCancel' + id).hide();
	$('#spanCancel' + id).show();
	$.ajax({
			 type: "GET",
			 url: webRoot + "ajax/cancel-subscription.php",
			 data: "id=" + id,
			 error: function (xhr, status, error) {
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
			   if (data == "") {
			   	$('#lnkCancel' + id).hide();
				$('#spanCancel' + id).text("Marked for Cancellation");

			   } else {
			   	 alert(data);
			   } 
			} // function(data)
		});

 
}


