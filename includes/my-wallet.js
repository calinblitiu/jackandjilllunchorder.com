
$(document).ready(function () {

	$('#btnStart').click(function () {
		if (tokenId == null || tokenId == '') { 
			$("input").removeClass("error-border");
			$('input').formError({remove:true,  successImage: {enabled:false}});
			$('#dummy-popup').modal("show");
		} else {
			$("input").removeClass("error-border");
			$('input').formError({remove:true,  successImage: {enabled:false}});
			$('#token-popup').modal("show");
		
		}
	});

	if (error_message != "") {
		$('#error-modal').modal("show");
	}

	if (success_message != "") {
		$('#success-modal').modal("show");
	}

	if (success == "1") {
		$('#success-modal').modal("show");
	}


	$('input').focus(function() {
		$(this).removeClass("error-border");
		$(this).formError({remove:true,  successImage: {enabled:false}});

		$("input").removeClass("error-border");
		$('input').formError({remove:true,  successImage: {enabled:false}});

	});


	$('#btnAuto').click(function () {
		avalidate();
	});
})


function cvalidate() {
	var retVal = true;

	var frm = document.frmCredit;
	var namec = frm.namec.value;
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

	if (amount == null || amount == "" || isNaN(amount) || parseInt(amount) < 1 ||
			amount.indexOf(".") > -1) {
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
	
	$('#btnPay').hide();
	$('#btnModalClose').hide();
	$('#imgLoader').show();

	var data = "amt=" + amount + "&cc=" + eCrypt.encryptValue(cc) + "&mm=" + mm + "&yyyy=" + yyyy + "&cvv=" + eCrypt.encryptValue(cvv) + "&namec=" + namec;
		alert(data);
	
	    $.ajax({
			 type: "POST",
			 url: webRoot + "ajax/add-to-wallet.php",
			 data: data,
			 error: function (xhr, status, error) {
				 	alert(xhr +"," + status +"," + error);
			 },
			 success: function(data){

			   $('#btnPay').show();
			   $('#btnModalClose').show();
			   $('#imgLoader').hide();

			   if (data == "") {
				  $('#success-modal').modal("show");

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
	//var cvv = frm.cvv2.value;
	if (amount == null || amount == "" || isNaN(amount) || parseInt(amount) < 1 ||
			amount.indexOf(".") > -1) {
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

	/*if (cvv== null || cvv == "" || isNaN(cvv) || cvv.length != 3) {
		$('#cvv2').addClass("error-border");
		$("#cvv2").formError(
			"CVV should be 3 digits",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#cvv2').removeClass("error-border");
		$("#cvv2").formError({remove:true,  successImage: {enabled:false}});

	}*/
	

	if (!retVal) {
		return false;
	}
	$('#btnPay2').hide();
	$('#btnModalClose2').hide();
	$('#imgLoader2').show();

	var data = "amt=" + amount;
	
	    $.ajax({
			 type: "POST",
			 url: webRoot + "ajax/add-to-wallet-via-token.php",
			 data: data,
			 error: function (xhr, status, error) {
				 	alert(xhr +"," + status +"," + error);
			 },
			 success: function(data){

			   $('#btnPay2').show();
			   $('#btnModalClose2').show();
			   $('#imgLoader2').hide();

			   if (data == "") {
				  $('#success-modal').modal("show");

			   } else {
			   	  $('#errorMessage').html("<b>" + data + "</b>");
				  $('#error-modal').modal("show");
			   }
			} // function(data)
		});

	return false;

}



function avalidate() {
	var retVal = true;

	var amount = $('#auto_recharge').val();
	if (amount == null || amount == "" || isNaN(amount) || parseInt(amount) < 1 ||
			amount.indexOf(".") > -1) {
		$('#auto_recharge').addClass("error-border");
		$("#auto_recharge").formError(
			"Amount should be a valid number without decimal places",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#auto_recharge').removeClass("error-border");
		$("#auto_recharge").formError({remove:true,  successImage: {enabled:false}});

	}


	if (!retVal) {
		return false;
	}
	var data = "amt=" + amount;
;
	
	    $.ajax({
			 type: "POST",
			 url: webRoot + "ajax/update-auto-charge-amount.php",
			 data: data,
			 error: function (xhr, status, error) {
				 	alert(xhr +"," + status +"," + error);
			 },
			 success: function(data){


			   if (data == "") {
				  $('#success-auto-modal').modal("show");

			   } else {
			   	  $('#errorMessage').html("<b>" + data + "</b>");
				  $('#error-modal').modal("show");
			   }
			} // function(data)
		});

	return false;

}


function cvalidate() {
	var retVal = true;

	var frm = document.frmCredit;
	var namec = frm.namec.value;
	var amount = frm.amount.value;

	var card_number = frm.EWAY_CARDNUMBER.value;
	var mm = frm.mm.value;
	var yyyy = frm.yyyy.value;
	var card_cvn = frm.EWAY_CARDCVN.value;
	

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

	if (amount == null || amount == "" || isNaN(amount) || parseInt(amount) < 1 ||
			amount.indexOf(".") > -1) {
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

	if (card_number.trim().length < 16) {
		$('#card_number').addClass("error-border");
		$("#card_number").formError(
			"Card must be 16 characters",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#card_number').removeClass("error-border");
		$("#card_number").formError({remove:true,  successImage: {enabled:false}});

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
	if (card_cvn== null || card_cvn == "" || isNaN(card_cvn) || card_cvn.length != 3) {
		$('#card_cvn').addClass("error-border");
		$("#card_cvn").formError(
			"CVV should be 3 digits",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#card_cvn').removeClass("error-border");
		$("#card_cvn").formError({remove:true,  successImage: {enabled:false}});

	}

	if (!retVal) {
		return false;
	}
	return true;
	
	$('#btnPay').hide();
	$('#btnModalClose').hide();
	$('#imgLoader').show();

	if (sandbox == 0) {
		card_number = eCrypt.encryptValue(card_number);
		card_cvn = eCrypt.encryptValue(card_cvn);
	}
	var data = "amt=" + amount + "&EWAY_CARDNUMBER=" + card_number + "&mm=" + mm + "&yyyy=" + yyyy + "&EWAY_CARDCVN=" + card_cvn +	"&namec=" + namec;
	
	    $.ajax({
			 type: "POST",
			 url: webRoot + "ajax/add-to-wallet.php",
			 data: data,
			 error: function (xhr, status, error) {
				 	alert(xhr +"," + status +"," + error);
			 },
			 success: function(data){

			   $('#btnPay').show();
			   $('#btnModalClose').show();
			   $('#imgLoader').hide();

			   if (data == "") {
				  $('#success-modal').modal("show");

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
	//var cvv = frm.cvv2.value;
	if (amount == null || amount == "" || isNaN(amount) || parseInt(amount) < 1 ||
			amount.indexOf(".") > -1) {
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

	/*if (cvv== null || cvv == "" || isNaN(cvv) || cvv.length != 3) {
		$('#cvv2').addClass("error-border");
		$("#cvv2").formError(
			"CVV should be 3 digits",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#cvv2').removeClass("error-border");
		$("#cvv2").formError({remove:true,  successImage: {enabled:false}});

	}*/
	
	if (!retVal) {
		return false;
	}
	$('#btnPay2').hide();
	$('#btnModalClose2').hide();
	$('#imgLoader2').show();

	var data = "amt=" + amount;

	
	    $.ajax({
			 type: "POST",
			 url: webRoot + "ajax/add-to-wallet-via-token.php",
			 data: data,
			 error: function (xhr, status, error) {
				 	alert(xhr +"," + status +"," + error);
			 },
			 success: function(data){

			   $('#btnPay2').show();
			   $('#btnModalClose2').show();
			   $('#imgLoader2').hide();

			   if (data == "") {
				  $('#success-modal').modal("show");

			   } else {
			   	  $('#errorMessage').html("<b>" + data + "</b>");
				  $('#error-modal').modal("show");
			   }
			} // function(data)
		});

	return false;

}


function useCard() {
	var amount = $('#amount2').val();
	$('#amount').val(amount);
	$('#token-popup').modal("toggle");
	$('#dummy-popup').modal("show");

}
