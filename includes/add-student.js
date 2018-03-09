$(document).ready(function() {
	
	$('#dob').datepicker({
	    format: 'yyyy-mm-dd'
	});


	$('#schools').change(function() {
		var id = $(this).val();
		getClasses(id, $('#classid').val());
	});	  		

	
	if (error_message != "") {
		$('#error-modal').modal("show");
	}

	if (success_message != "") {
		$('#success-modal').modal("show");
	}

	if (parseInt($('#xid').val()) > 0) {
		var schoolId = $( "#schools option:selected" ).attr("value");

		getClasses(schoolId, $('#classid').val());
	}

	$('input').focus(function() {
		$(this).removeClass("error-border");
		$(this).formError({remove:true,  successImage: {enabled:false}});

		$("#schoolsdiv, #classesdiv, #datef").removeClass("error-border");
		$("#schoolsdiv, #classesdiv, #datef").formError({remove:true,  successImage: {enabled:false}});
	
		$("input").removeClass("error-border");
		$('input').formError({remove:true,  successImage: {enabled:false}});

	});

	$('.nice-select').focus(function() {
		$("#schoolsdiv, #classesdiv, #datef").removeClass("error-border");
		$("#schoolsdiv, #classesdiv, #datef").formError({remove:true,  successImage: {enabled:false}});
	
		$("input").removeClass("error-border");
		$('input').formError({remove:true,  successImage: {enabled:false}});

	});



 });


function getClasses(id, select_id) {
			$.ajax({
			 type: "GET",
			 url: webRoot + "ajax/get-school-enabled-classes.php?id=" + id,
			 data: '',
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
			    var cbo = document.getElementById("classes");
				cbo.options.length = 0;
				
				$('#classes').append('<option value="0">Select a Class</option>');

			    var result = $.parseJSON(data);
				if (result != null) {
				  for(var i = 0; i < result.length; i++) {
				   var sel = "";
				   if (result[i].ID == select_id)
				     sel = " selected ";
				   $('#classes').append('<option value="'+ result[i].ID +'"' + sel + '>'+ result[i].name+'</option>');
				  }
				  $('#classes').niceSelect("update");
				}
			} // function(data)
		});
}

function xvalidate() {
	var retVal = true;

	var frm = document.frm;
	var name = frm.xname.value;
	var school = $( "#schools option:selected" ).attr("value");
	var classes = $( "#classes option:selected" ).attr("value");

	var dob = frm.dob.value;

	if (name.trim().length < 3) {
		$('#xname').addClass("error-border");
		$("#xname").formError(
			"Name cannot be less than 3 characters",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#xname').removeClass("error-border");
		$("#xname").formError({remove:true,  successImage: {enabled:false}});

	}

	if (school === undefined || school == "0") {
		$('#schoolsdiv').addClass("error-border");
		$("#schoolsdiv").formError(
			"Please select a school",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#schoolsdiv').removeClass("error-border");
		$("#schoolsdiv").formError({remove:true,  successImage: {enabled:false}});

	}

	if (classes === undefined || classes == "" || classes == "0") {
		$('#classesdiv').addClass("error-border");
		$("#classesdiv").formError(
			"Please select a class",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#classesdiv').removeClass("error-border");
		$("#classesdiv").formError({remove:true,  successImage: {enabled:false}});

	}


	if (dob == "") {
		$('#dob').addClass("error-border");
		$("#dob").formError(
			"Date of birth is required",
			{  remove:true,   successImage: {enabled:false}
			});
		retVal = false;
	} else {
		$('#dob').removeClass("error-border");
		$("#dob").formError({remove:true,  successImage: {enabled:false}});

	}


	if (!retVal) {
		return false;
	}

	return true;

}


