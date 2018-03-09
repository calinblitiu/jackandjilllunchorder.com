
$(document).ready(function () {

		$('#btnCancel').click(function () {
			window.location = "containers.php";
		});
		
		$('#btnRight').click(function () {
			var allItem = $('#allitems').val();
			if (allItem == null || allItem == "") {
				alert("No item selected to add");
			} else {
				addSelItem(document.frmCont.xid.value, allItem, 1);
			}
		});
		$('#btnLeft').click(function () {
			var selItem = $('#selitems').val();
			if (selItem == null || selItem == "") {
				alert("No item selected to remove");
			} else {
				addSelItem(document.frmCont.xid.value, selItem, -1);
			}
		});
		
		loadAllItems($('#ctype').val());
		if (document.frmCont.xid.value != "0")
			loadSelItems(document.frmCont.xid.value);

})

function changeAllItems() {
	loadAllItems($('#ctype').val());
	$('#selItems').html("");
}

function xvalidate() {
	
	var message = "<ul>";
	var retVal = true;

	var frm = document.frmCont;
	var id = frm.xid.value;
	var name = frm.name.value;
	
	if (name.trim().length < 3) {
		message += "<li>Container name should be at least 3 characters.</li>";
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

function loadAllItems(type) {
		
		showLoader();
			$.ajax({
			 type: "POST",
			 url: "../ajax/get-menu-items-food-type.php",
			 data: "type=" + type,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				hideLoader();
				var result = $.parseJSON(data);
				var count = result.length;
				
				var html = "";
				for (var i =0; i < count; i++) {
					html += "<option value=\"" + result[i].ID + "\">" +
							result[i].name + "</option>";
				}
				$('#allitems').html(html);
				if (type == "HOT")
					$('#spanAllItems').html("All Hot Items");
				else if (type == "COLD")
					$('#spanAllItems').html("All Cold Items");
				

			} // function(data)
		});
}


function addSelItem(container_id, item_id, qty) {
		var data = "container_id=" + container_id + "&item_id=" + item_id + "&qty=" + qty;
		
		showLoader();
			$.ajax({
			 type: "POST",
			 url: "../ajax/add-item-to-container.php",
			 data: data,
			 error: function (xhr, status, error) {
			 		//hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				hideLoader();
				
				if (data == "") {
					loadSelItems(container_id);
				} else {
					alert(data);
				}
			} // function(data)
		});
}


function loadSelItems(container_id) {
		var data = "container_id=" + container_id;
		showLoader();
			$.ajax({
			 type: "POST",
			 url: "../ajax/get-sel-items-of-container.php",
			 data: data,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				hideLoader();
				var result = $.parseJSON(data);
				var count = result.length;
				
				var html = "";
				for (var i =0; i < count; i++) {
					html += "<option value=\"" + result[i].product_id + "\">" +
							result[i].productname + " (" + result[i].qty + ")</option>";
				}
				$('#selitems').html(html);
				

			} // function(data)
		});
}



