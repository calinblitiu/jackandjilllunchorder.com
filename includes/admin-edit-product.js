
$(document).ready(function () {

		$('#btnCancel').click(function () {
			window.location = "menu.php";
		});


 //  Image upload Handler:
	    var url = "../items/index.php";
	    $('#flImage').fileupload({
	        url: url,
	        dataType: 'json',
			add: function (e, data) {
				var isvalid = true;
				$.each(data.files, function (index, file) {
					isvalid = (/\.(gif|jpg|jpeg|tiff|png)$/i).test(file.name);
			    });
				if (isvalid) {
					$('#progress').show();
					data.submit();
				} else {
					$('#progress .progress-bar').css('width', '0%');
					$('#files').html("");
				}
			},
	        done: function (e, data) {
	            $.each(data.result.files, function (index, file) {
	                $('<p/>').text(file.name).appendTo('#files');
			$('#file_image').attr("value", file.name);
			$('#imgPreview').attr("src", "../items/files/" + file.name);
			$('#imgPreview').show();
	            });
	        },
	        progressall: function (e, data) {
	            var progress = parseInt(data.loaded / data.total * 100, 10);
	            $('#progress .progress-bar').css(
	                'width',
	                progress + '%'
	            );
	        }
		    }).prop('disabled', !$.support.fileInput)
    		    .parent().addClass($.support.fileInput ? undefined : 'disabled');
		
		
	 if (document.frmProd.xid.value != "0") {
		getIngredients(document.frmProd.xid.value);
		getNutritionItems(document.frmProd.xid.value);
		getAllergies(document.frmProd.xid.value);
		
	 }

	 $('#btnCancel4').click(function () {
		window.location = "menu.php";
	 })

	 $('#btnSubmit4').click(function () {
		saveIngredients(document.frmProd.xid.value);
	 })

	 $('#lnkNewNutrition').click(function () {
		initNutriPopup(0);
		$('#nutri-popup').modal("show");
	 })
	 $('#lnkNewAllergy').click(function () {
		initAllergyPopup(0);
		$('#allergy-popup').modal("show");
	 })

	 $('#btnNSave').click(function() {
		 nValidate();
	 });

	 $('#btnASave').click(function() {
		 aValidate();
	 });
})

function initNutriPopup(id) {
	$('#xxid').val("0");
	$('#crop_type').val("");
	$('#yield').val("");
	$('#percent').val("");
	$('#adjusted').val("");
	$('#protein').val("");
	$('#fat').val("");
	$('#carbs').val("");
	$('#fiber').val("");
	$('#time').val("");
	$('#protein').val("");
		
}

function xvalidate() {
	
	var message = "<ul>";
	var retVal = true;

	var frm = document.frmProd;
	var id = frm.xid.value;
	var name = frm.name.value;
	var price = frm.price.value;
	var descrip = frm.descrip.value;
	var ftype = frm.ftype.value;
	var ckRecess = 0;
	if (frm.ckRecess.checked)
		ckRecess = 1;
	var ckLunch = 0;
	if (frm.ckLunch.checked)
		ckLunch = 1;
	var ckGlobal = 0;
	if (frm.ckGlobal.checked)
		ckGlobal = 1;
	var image = frm.file_image.value;
	var ccode = frm.ccode.value;

	
	if (name.trim().length < 3) {
		message += "<li>Name cannot be less than 3 characters.</li>";
		retVal = false;
	}

	if (ftype == null || ftype == "") {
		message += "<li>Food Type is required</li>";
		retVal = false;
	}

	if (price == null || price == "" || isNaN(price)) {
		message += "<li>Price looks invalid.</li>";
		retVal = false;
	}	
	if (ckRecess == 0 && ckLunch == 0) {
		message += "<li>Item has to be either in Recess or Lunch menu</li>";
		retVal = false;
	}
	

	if (image == null || image == "") {
		message += "<li>Item needs an image.</li>";
		retVal = false;
	}

	if (ccode == null || ccode == "") {
		message += "<li>Canteen Code is required</li>";
		retVal = false;
	}

	message += "</ul>";
	
	if (!retVal) {
		jQuery('#errorContent').html(message);
		jQuery('#errorLabel').text("Form Errors");
		jQuery('#errors-popup').modal();
		return false;
	}

	return true;

}


function getIngredients(id) {
		showLoader();
		
			$.ajax({
			 type: "GET",
			 url: "../ajax/get-item-ingredients.php?id=" + id,
			 data: '',
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				hideLoader();
			        var result = $.parseJSON(data);
				if (result.data != null)
				  $('#fingredients').val(result.data);
			} // function(data)
		});
}

function saveIngredients(id) {
		showLoader();
		var content = $('#fingredients').val();
			$.ajax({
			 type: "POST",
			 url: "../ajax/save-item-ingredients.php",
			 data: "id=" + id + "&content=" + content,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				hideLoader();
				if (data != "")
					alert(data);
				else
				  window.location = "menu.php";
			} // function(data)
		});
}

function getNutritionItems(id) {
		showLoader();
			$.ajax({
			 type: "GET",
			 url: "../ajax/get-item-nutritions.php?id=" + id,
			 data: '',
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				hideLoader();
			        var result = $.parseJSON(data);
				if (result != null) {
				    var count = result.length;
				    $('#ntotal').text(result.length);
				
				   var html = "";
  			            for (var i = 0; i < count; i ++) {
				        var entry = result[i];
					html += "<tr>";
					html += "<td class=\"col-sm-2\"><a href=# onclick='ndel(" + entry.ID + "); return false;'><i class='fg-red fa fa-minus-square'></i></a>&nbsp;&nbsp;&nbsp;<a href=# onclick='nedit(" + entry.ID + "); return false;'>" + entry.crop_type + "</a></td>";
					html += "<td class=\"col-sm-1\">" + entry.yield + "</td>";
					html += "<td class=\"col-sm-1\">" + entry.percent + "</td>";
					html += "<td class=\"col-sm-1\">" + entry.adjusted + "</td>";
					html += "<td class=\"col-sm-1\">" + entry.protein + "</td>";
					html += "<td class=\"col-sm-1\">" + entry.fat + "</td>";
					html += "<td class=\"col-sm-1\">" + entry.carbs +"</td>";
					html += "<td class=\"col-sm-1\">" + entry.fiber + "</td>";
					html += "<td class=\"col-sm-1\">" + entry.xtime + "</td>";
					html += "<td class=\"col-sm-1\">" + entry.proteiny + "</td>";
					html += "</tr>";
				     } 
				     $("#tblNutrition > tbody").html("");
				     $("#tblNutrition tbody").append(html);
				} else {
				  $('#ntotal').text(result.length);
				}
			} // function(data)
		});
}


function nValidate() {
	
	var message = "<ul>";
	var retVal = true;

	var frm = document.frmNutri;
	var id = frm.xxid.value;
	var crop_type = frm.crop_type.value;
	var yield = frm.yield.value;
	var percent = frm.percent.value;
	var adjusted  = frm.adjusted.value;
	var protein = frm.protein.value;
	var fat = frm.fat.value;
	var carbs = frm.carbs.value;
	var fiber = frm.fiber.value;
	var xtime = frm.xtime.value;
	var proteiny = frm.proteiny.value;

	data = "id=" + frm.xxid.value + 
		"&pid=" + document.frmProd.xid.value +
		"&crop_type=" + crop_type +
		"&yield=" + yield +
		"&percent=" + percent +
		"&adjusted=" + adjusted + 
		"&protein=" + protein +
		"&fat=" + fat +
		"&carbs=" + carbs +
		"&fiber=" + fiber + 
		"&xtime=" + xtime + 
		"&proteiny=" + proteiny;
	
	if (crop_type.trim().length < 1) {
		message += "<li>Crop Type cannot be empty.</li>";
		retVal = false;
	}


	message += "</ul>";
	
	if (!retVal) {
		jQuery('#errorContent').html(message);
		jQuery('#errorLabel').text("Form Errors");
		jQuery('#errors-popup').modal();
		return false;
	}

	$('#nutri-popup').modal("toggle");

	saveNutri(data);
	return false;
}

function saveNutri(data) {
		showLoader();
			$.ajax({
			 type: "POST",
			 url: "../ajax/save-item-nutrition.php",
			 data: data,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				hideLoader();
				if (data != "")
					alert(data);
				else {
				   initNutriPopup(0);
				   getNutritionItems(document.frmProd.xid.value);
				}

			} // function(data)
		});
}

function ndel(xxid) {
   if (confirm("Are you sure?")) {
   	var product_id = document.frmProd.xid.value;
	var data = "id=" + xxid + "&pid=" + product_id;
		showLoader();
			$.ajax({
			 type: "GET",
			 url: "../ajax/del-item-nutrition.php",
			 data: data,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				hideLoader();
				if (data != "")
					alert(data);
				else
				   getNutritionItems(document.frmProd.xid.value);

			} // function(data)
		});
  }
}

function nedit(xxid) {
   	var product_id = document.frmProd.xid.value;
	var data = "id=" + xxid + "&pid=" + product_id;
		showLoader();
			$.ajax({
			 type: "GET",
			 url: "../ajax/get-item-nutrition.php",
			 data: data,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				hideLoader();
			        var result = $.parseJSON(data);
				if (result != null) {
					var entry = result;
					$('#xxid').val(entry.ID);
					$('#crop_type').val(entry.crop_type);
					$('#protein').val(entry.protein);
					$('#yield').val(entry.yield);
					$('#percent').val(entry.percent);
					$('#adjusted').val(entry.adjusted);
					$('#protein').val(entry.protein);
					$('#fat').val(entry.fat);
					$('#carbs').val(entry.carbs);
					$('#fiber').val(entry.fiber);
					$('#time').val(entry.time);
					$('#proteiny').val(entry.proteiny);
					$('#nutri-popup').modal("show");
				}
	
			} // function(data)
		});
 
}



function initAllergyPopup(id) {
	$('#xxxid').val("0");
	$('#cboAllergy').val("");
	$('#flag').prop("checked", false);
		
}



function getAllergies(id) {
		showLoader();
			$.ajax({
			 type: "GET",
			 url: "../ajax/get-items-allergies.php?id=" + id,
			 data: '',
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				hideLoader();
			        var result = $.parseJSON(data);
				if (result != null) {
				    var count = result.length;
				    $('#atotal').text(result.length);
				
				   var html = "";
  			            for (var i = 0; i < count; i ++) {
				        var entry = result[i];
					html += "<tr>";
					html += "<td class=\"col-sm-8\"><a href=# onclick='adel(" + entry.ID + "); return false;'><i class='fg-red fa fa-minus-square'></i></a>&nbsp;&nbsp;&nbsp;<a href=# onclick='aedit(" + entry.ID + "); return false;'>" + entry.name+ "</a></td>";
					html += "<td class=\"col-sm-4\">";
					if (entry.flag == 1)
					   html += "<i class='fa fa-check-square fg-green'></i>";
					else
					   html += "<i class='fa fa-close fg-red'></i>";
					html += "</td>";
					html += "</tr>";
				     } 
				     $("#tblAllergies > tbody").html("");
				     $("#tblAllergies tbody").append(html);
				} else {
				  $('#atotal').text(result.length);
				}
			} // function(data)
		});
}


function aValidate() {
	
	var message = "<ul>";
	var retVal = true;

	var frm = document.frmAllergy;
	var id = frm.xxxid.value;
	var allergy = frm.cboAllergy.value;
	var flag = 0;
	if ($('#flag').prop("checked"))	
		flag = 1;

	data = "id=" + frm.xxxid.value + 
		"&pid=" + document.frmProd.xid.value +
		"&allergy=" + allergy +
		"&flag=" + flag;
	
	if (allergy.trim().length < 1) {
		message += "<li>Allergy cannot be empty.</li>";
		retVal = false;
	}


	message += "</ul>";
	
	if (!retVal) {
		jQuery('#errorContent').html(message);
		jQuery('#errorLabel').text("Form Errors");
		jQuery('#errors-popup').modal();
		return false;
	}

	$('#allergy-popup').modal("toggle");

	saveAllergy(data);
	return false;
}


function saveAllergy(data) {
		
		showLoader();
			$.ajax({
			 type: "POST",
			 url: "../ajax/save-item-allergy.php",
			 data: data,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				hideLoader();
				if (data != "")
					alert(data);
				else {
				   initNutriPopup(0);
				   getAllergies(document.frmProd.xid.value);
				}

			} // function(data)
		});
}


function adel(xxid) {
   if (confirm("Are you sure?")) {
   	var product_id = document.frmProd.xid.value;
	var data = "id=" + xxid + "&pid=" + product_id;
		showLoader();
			$.ajax({
			 type: "GET",
			 url: "../ajax/del-item-allergies.php",
			 data: data,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				hideLoader();
				if (data != "")
					alert(data);
				else
				   getAllergies(document.frmProd.xid.value);

			} // function(data)
		});
  }
}

function aedit(xxid) {
   	var product_id = document.frmProd.xid.value;
	var data = "id=" + xxid + "&pid=" + product_id;
		showLoader();
			$.ajax({
			 type: "GET",
			 url: "../ajax/get-item-allergies.php",
			 data: data,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				hideLoader();
			        var result = $.parseJSON(data);
				if (result != null) {
					var entry = result;
					$('#xxxid').val(entry.ID);
					$('#cboAllergy').val(entry.name);
					if (entry.flag == 1)
					  $('#flag').prop("checked", true);
					else
					  $('#flag').prop("checked", false);

					$('#allergy-popup').modal("show");
				}
	
			} // function(data)
		});
 
}
