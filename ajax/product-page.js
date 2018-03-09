
$(document).ready(function () {

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
 var pid = frm.pid.value;
 var qty = $('#qty').val();
 var mealType = $('#mtype').val();
 var price = $('#price').val();
 
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

			   if (data == "") {
			   	  $('#success-modal').modal("show");
			   } else {
			   	  $('#errorMessage').html("<b>" + data + "</b>");
				  $('#error-modal').modal("show");
			   }
			} // function(data)
		});

}

function back() {
 window.location = webRoot + "products-list";
}


