
$(document).ready(function () {

	$('#btnNoSubs').click(function() {
		$('#subscription_popup').modal("hide");
		$('#checkout_popup').modal("show");
		doCheckout();
	});

	$('#lnkNotes').click(function() {
		$('#notes_popup').modal("show");
	});
})


function doProcess() {

   $.ajax({
			 type: "GET",
			 url: webRoot + "ajax/validate-checkout.php",
			 data: "",
			 error: function (xhr, status, error) {

				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
			 	if (data != "OK") {
					var arr = data.split(':');
					var creditTotal = arr[1];
					var cartTotal = arr[2];
					var diff = parseFloat(cartTotal) - parseFloat(creditTotal);
					
					var message = "Your wallet does not enough balance.<br>";
					message += " Please add money to complete this order";
					$('#pmessage').html(message);
					$('#nobalance_popup').modal("show");
				} else {
					$('#subscription_popup').modal();
				}
			  
			} // function(data)
		});

	

}

function subscriptionPopup() {

 $('#subscription_popup').modal("show");
}


function doDel(id) {
  if (confirm("Are you sure?")) {
  	window.location = "cart?del=" + id;
  }
}

function recalc() {

  $('.price').each(function() {
  	 var thisId = $(this).attr("id");
	 thisId = thisId.replace(/price/, "");
	 var thisPrice = $(this).text();
	 thisPrice = thisPrice.replace(/\$/, "");

	 var thisQty = $('#qty' + thisId).val();
	 
	 var thisTotal = parseFloat(thisPrice) * parseFloat(thisQty);
	 $('#divItemTotal' + thisId).html("$" + thisTotal.toFixed(2));

 	 var data = "id=" +  thisId + "&qty=" + thisQty;
     $.ajax({
			 type: "POST",
			 url: webRoot + "ajax/cart-update.php",
			 data: data,
			 error: function (xhr, status, error) {

				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){

			   if (data == "") {
			   } else {
			   	  $('#errorMessage').html("<b>" + data + "</b>");
				  $('#error-modal').modal("show");
			   }
			} // function(data)
		});

	
  });

  var subtotal = 0;
  $('.divItemTotal').each(function() {
    var thisTotal = $(this).text();
	thisTotal = thisTotal.replace(/\$/, "");
	subtotal += parseFloat(thisTotal);
  });
  $('#divSubTotal').html("$" + subtotal.toFixed(2));
  $('#divTotal').html("$" + subtotal.toFixed(2));

}

function doCheckout() {
   var data = "notes=" + document.frmNotes.notes.value;
   $.ajax({
			 type: "POST",
			 url: webRoot + "ajax/do-checkout.php",
			 data: data,
			 error: function (xhr, status, error) {
			   		$('#checkout_popup').modal("hide");
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
		 		$('#checkout_popup').modal("hide");

			 	if (data != "") {
					alert(data);
				} else {
					window.location = "order-confirmation";
				}

			  
			} // function(data)
		});

	

}

function saveNotes() {
   $('#notes_popup').modal("hide");
}
