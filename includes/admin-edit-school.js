
$(document).ready(function () {

	$('#date').datepicker({
    	format: 'yyyy-mm-dd'
	});

		$('#btnCancel').click(function () {
			window.location = "schools.php";
		});


 //  Image upload Handler:
	    var url = "../schools/index.php";
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
			$('#imgPreview').attr("src", "../schools/files/" + file.name);
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
		
		

	 $('#btnAddClass').click(function () {
		initClassPopup();
		$('#class-popup').modal("show");
	 })
		
	 $('#btnCSave').click(function() {
		 cValidate();
	 });
		
	 $('#btnAddOffDays').click(function () {
		initOffDaysPopup();
		$('#offdays-popup').modal("show");
	 })
		
	 $('#btnODSave').click(function() {
		 odValidate();
	 });
	
	 $('#btnAddItem').click(function () {
		initItemPopup();
		$('#items-popup').modal("show");
	 })
	
	 $('#btnISave').click(function() {
		 iValidate();
	 });
	

	 if (document.frmSchool.xid.value != "0") {
	 	getClasses(document.frmSchool.xid.value);
	 	getOffDays(document.frmSchool.xid.value);
		getItems(document.frmSchool.xid.value);

	 }
})


function xvalidate() {
	
	var message = "<ul>";
	var retVal = true;

	var frm = document.frmSchool;
		var id = frm.xid.value;
	var name = frm.name.value;
	var descrip = frm.descrip.value;
	var address = frm.address.value;
	var city = frm.city.value;

	var image = frm.file_image.value;

	
	if (name.trim().length < 5) {
		message += "<li>Name cannot be less than 5 characters.</li>";
		retVal = false;
	}

	if (address.trim().length < 10) {
		message += "<li>Address is missing or incomplete.</li>";
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


function initClassPopup(id) {
	$('#xxid').val("0");
	$('#sclass').val("");
	$('#flag').prop("checked", false);	
}


function cValidate() {
	
	var message = "<ul>";
	var retVal = true;

	var frm = document.frmClass;
	var id = frm.xxid.value;
	var sclass = frm.sclass.value;
	var flag = 0;
	if ($('#flag').prop("checked"))	
		flag = 1;

	data = "id=" + frm.xxid.value + 
		"&sid=" + document.frmSchool.xid.value +
		"&class=" + sclass +
		"&flag=" + flag;
	
	if (sclass.trim().length < 1) {
		message += "<li>Class cannot be empty.</li>";
		retVal = false;
	}


	message += "</ul>";
	if (!retVal) {
		jQuery('#errorContent').html(message);
		jQuery('#errorLabel').text("Form Errors");
		jQuery('#errors-popup').modal();
		return false;
	}

	$('#class-popup').modal("toggle");

	saveClass(data);
	return false;
}


function saveClass(data) {
		
		showLoader();
			$.ajax({
			 type: "POST",
			 url: "../ajax/save-school-class.php",
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
				   initClassPopup();
				   getClasses(document.frmSchool.xid.value);
				}

			} // function(data)
		});
}


function cdel(xxid) {
   if (confirm("Are you sure?")) {
   	var school_id = document.frmSchool.xid.value;
	var data = "id=" + xxid + "&sid=" + school_id;
		showLoader();
			$.ajax({
			 type: "GET",
			 url: "../ajax/del-school-class.php",
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
				   getClasses(document.frmSchool.xid.value);

			} // function(data)
		});
  }
}

function cedit(xxid) {
   	var school_id = document.frmSchool.xid.value;
	var data = "id=" + xxid + "&sid=" + school_id;
		showLoader();
			$.ajax({
			 type: "GET",
			 url: "../ajax/get-school-class.php",
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
					$('#sclass').val(entry.name);
					if (entry.flag == 1)
					  $('#flag').prop("checked", true);
					else
					  $('#flag').prop("checked", false);

					$('#class-popup').modal("show");
				}
	
			} // function(data)
		});
 
}

function getClasses(id) {
		showLoader();
			$.ajax({
			 type: "GET",
			 url: "../ajax/get-school-classes.php?id=" + id,
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
					html += "<td class=\"col-sm-6\"><a href=# onclick='cdel(" + entry.ID + "); return false;'><i class='fg-red fa fa-minus-square'></i></a>&nbsp;&nbsp;&nbsp;<a href=# onclick='cedit(" + entry.ID + "); return false;'>" + entry.name+ "</a></td>";
					html += "<td class=\"col-sm-4\">";
					if (entry.flag == 1)
					   html += "<a id=\"toggle" + entry.ID +"\" href=# onclick=\"toggle(" + entry.ID +", 0); return false;\"><i class='fa fa-check-square fg-green'></i></a>";
					else
					   html += "<a id=\"toggle" + entry.ID +"\" href=# onclick=\"toggle(" + entry.ID +", 1); return false;\"><i class='fa fa-close fg-red'></i></a>";
					html += "</td>";
					html += "</tr>";
				     } 
				     $("#tblClasses > tbody").html("");
				     $("#tblClasses tbody").append(html);
				} else {
				}
			} // function(data)
		});
}


function toggle(id, flag) {
		var data = "id=" + id + "&flag=" + flag;
			$.ajax({
			 type: "GET",
			 url: "../ajax/toggle-school-class.php",
			 data: data,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				if (data != "")
					alert(data);
				else {
				  if (flag == 1) {
				  	$('#toggle' + id).html("<i class=\"fa fa-check-square fg-green\"></i>");
					$("#toggle" + id).attr("onclick","toggle(" + id + ",0); return false;");

				  } else {
				  	$('#toggle' + id).html("<i class=\"fa fa-close fg-red\"></i>");
					$("#toggle" + id).attr("onclick","toggle(" + id + ",1); return false;");

				  }
				}

			} // function(data)
		});

  return false;
}

function initOffDaysPopup(id) {
	$('#xxxid').val("0");
	$('#reason').val("");
	$('#date').val("");
}

function odValidate() {
	
	var message = "<ul>";
	var retVal = true;

	var frm = document.frmOD;
	var id = frm.xxxid.value;
	var reason = frm.reason.value;
	var date = frm.date.value;

	data = "id=" + frm.xxxid.value + 
		"&sid=" + document.frmSchool.xid.value +
		"&reason=" + reason +
		"&date=" + date;
	
	if (date.trim().length < 1) {
		message += "<li>Date must be entered.</li>";
		retVal = false;
	}


	message += "</ul>";
	if (!retVal) {
		jQuery('#errorContent').html(message);
		jQuery('#errorLabel').text("Form Errors");
		jQuery('#errors-popup').modal();
		return false;
	}

	$('#offdays-popup').modal("toggle");

	saveOffDays(data);
	return false;
}

function saveOffDays(data) {
		
		showLoader();
			$.ajax({
			 type: "POST",
			 url: "../ajax/save-school-offdays.php",
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
				   initOffDaysPopup();
				   getOffDays(document.frmSchool.xid.value);
				}

			} // function(data)
		});
}

function getOffDays(id) {
		showLoader();
			$.ajax({
			 type: "GET",
			 url: "../ajax/get-school-offdays.php?id=" + id,
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
					html += "<td class=\"col-sm-6\"><a href=# onclick='oddel(" + entry.ID + "); return false;'><i class='fg-red fa fa-minus-square'></i></a>&nbsp;&nbsp;&nbsp;" + entry.reason+ "</td>";
					html += "<td class=\"col-sm-4\">";
					html += "<a href=# onclick='odedit(" + entry.ID + "); return false;'>" + entry.date + "</a>";
					html += "</td>";
					html += "</tr>";
				     } 
				     $("#tblOffDays > tbody").html("");
				     $("#tblOffDays tbody").append(html);
				} else {
				}
			} // function(data)
		});
}

function oddel(xxid) {
   if (confirm("Are you sure?")) {
   	var school_id = document.frmSchool.xid.value;
	var data = "id=" + xxid + "&sid=" + school_id;
		showLoader();
			$.ajax({
			 type: "GET",
			 url: "../ajax/del-school-offdays.php",
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
				   getOffDays(document.frmSchool.xid.value);

			} // function(data)
		});
  }
}

function odedit(xxid) {
   	var school_id = document.frmSchool.xid.value;
	var data = "id=" + xxid + "&sid=" + school_id;
		showLoader();
			$.ajax({
			 type: "GET",
			 url: "../ajax/get-school-single-offdays.php",
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
					$('#reason').val(entry.reason);
					$('#date').val(entry.date);
					$('#offdays-popup').modal("show");
				}
	
			} // function(data)
		});
 
}

function initItemPopup(id) {
	$('#xxxxid').val("0");
	$('#item').val("");
}

function iValidate() {
	
	var message = "<ul>";
	var retVal = true;

	var frm = document.frmI;
	var id = frm.xxxxid.value;
	var item = frm.item.value;

	data = "id=" + frm.xxxxid.value + "&sid=" +  document.frmSchool.xid.value +
		"&pid=" + item;
	
	if (item == 0) {
		message += "<li>Item needs to be selected.</li>";
		retVal = false;
	}


	message += "</ul>";
	if (!retVal) {
		jQuery('#errorContent').html(message);
		jQuery('#errorLabel').text("Form Errors");
		jQuery('#errors-popup').modal();
		return false;
	}

	$('#items-popup').modal("toggle");

	saveItem(data);
	return false;
}


function saveItem(data) {
		
		showLoader();
			$.ajax({
			 type: "POST",
			 url: "../ajax/save-school-item.php",
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
				   initItemPopup();
				   getItems(document.frmSchool.xid.value);
				}

			} // function(data)
		});
}



function getItems(id) {
		showLoader();
			$.ajax({
			 type: "GET",
			 url: "../ajax/get-all-school-items.php?id=" + id,
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
				
				   var html = "";
  			      for (var i = 0; i < count; i ++) {
				    var entry = result[i];
					html += "<tr>";
					html += "<td class=\"col-sm-6\">" + entry.name+ "</td>";
					
					html += "<td class=\"col-sm-3\">";
					html += "<div class=\"checkbox\">";
					html += "<label>";
					html += "<input type=\"checkbox\" value=1 id=\"product" + entry.ID + "\" onchange=\"doToggle(" + entry.ID + ", " + id +"); return false;\" ";
					if (entry.disabled == 1)
						html += " checked=1";
					html += ">";
					html += "</label>";
					html += "</div>";
					html += "</td>";
					html += "</tr>";
				     } 
				     $("#tblItems > tbody").html("");
				     $("#tblItems tbody").append(html);
				} else {
				}
			} // function(data)
		});
}


function iedit(xxid) {
   	var school_id = document.frmSchool.xid.value;
	var data = "id=" + xxid + "&sid=" + school_id;
		showLoader();
			$.ajax({
			 type: "GET",
			 url: "../ajax/get-school-item.php",
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
					$('#xxxxid').val(entry.ID);
					$('#item').val(entry.product_id);

					$('#items-popup').modal("show");
				}
	
			} // function(data)
		});
 
}


function idel(xxid) {
   if (confirm("Are you sure?")) {
   	var school_id = document.frmSchool.xid.value;
	var data = "id=" + xxid + "&sid=" + school_id;
		showLoader();
			$.ajax({
			 type: "GET",
			 url: "../ajax/del-school-item.php",
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
				   getItems(document.frmSchool.xid.value);

			} // function(data)
		});
  }
}

function doToggle(pid, school_id) {
	var flag = 0;
	if ($("#product" + pid).prop("checked"))
	 flag = 1;
	
	var url = "../ajax/save-school-item.php";
	var data = "id=0&sid=" + school_id + "&pid=" + pid;
	
	if (flag == 0) {
		url = "../ajax/del-school-item-product.php";
		data = "sid=" + school_id + "&pid=" + pid;
	}

	$.ajax({
			 type: "POST",
			 url: url,
			 data: data,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				hideLoader();
				if (data != "")
					alert(data);

			} // function(data)
		});
	
}
