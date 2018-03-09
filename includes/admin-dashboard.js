	$(document).ready(function() {
	
		$('#deliv_date').datepicker({
	    	format: 'yyyy-mm-dd'
		});

		$('#deliv_date2').datepicker({
	    	format: 'yyyy-mm-dd'
		});


		getDueSubs(dueDate);

		$('#btnDueOrders').click(function() {
			var date = $('#deliv_date').val();
			if (date == null || date == "")
				return;
			getDueOrders(date);
		});
	  		
		$('#btnPrintDueOrders').click(function() {
			var date = $('#deliv_date').val();
			if (date == null || date == "")
				return;
			printLabels(date);
		});
			

		$('#btnDueSubs').click(function() {
			var date = $('#deliv_date2').val();
			if (date == null || date == "")
				return;
			getDueSubs(date);
		});
	  		
		$('#btnPrintDueSubs').click(function() {
			var date = $('#deliv_date2').val();
			if (date == null || date == "")
				return;
			printLabels2(date);
		});
			
   });

   function getDueOrders(date) {
   		var data = "date=" + date;
   		$.ajax({
			 type: "GET",
			 url: "../ajax/get-due-orders-for-date.php",
			 data: data,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){

			    var result = $.parseJSON(data);
				if (result != null) {
				    var count = result.length;
					if (count > 0)
						$('#btnPrintDueOrders').show();
					else
						$('#btnPrintDueOrders').hide();
					
					$('#divDelivHeading').html("Orders Due for Delivery (" + count + ")");
					
				   var html = "";
  			      for (var i = 0; i < count; i ++) {
				    var entry = result[i];
					html += "<tr>";
					html += "<td class=\"col-sm-2 text-left\">" + entry.ID+ "</td>";
					
					html += "<td class=\"col-sm-6 text-left\">";
					html += "<b>" + entry.student_name + "</b><br>";
					html += "Class " + entry.class_name + ", " + entry.school_name;
					html += "</td>";
					html += "</tr>";
				     } 
				     $("#tblDueOrders > tbody").html("");
				     $("#tblDueOrders tbody").append(html);
				} else {
					$('#btnPrintDueOrders').hide();
				}

			} // function(data)
		});

   }

   function printLabels(date) {
   		$('#lnkPrint').attr("href", data);
		$('#lnkPrint').hide();

   		var data = "date=" + date;
  	 	$.ajax({
			 type: "GET",
			 url: "../ajax/print-slips-for-a-date.php",
			 data: data,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				$('#lnkPrint').attr("href", data);
				$('#lnkPrint').show();
				
			} // function(data)
		});

   }

   function getDueSubs(date) {
   		var data = "date=" + date;
   		$.ajax({
			 type: "GET",
			 url: "../ajax/get-due-subscriptions-for-date.php",
			 data: data,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){

			    var result = $.parseJSON(data);
				if (result != null) {
				    var count = result.length;
					if (count > 0)
						$('#btnPrintDueSubs').show();
					else
						$('#btnPrintDueSubs').hide();
					
					$('#divDelivHeading2').html("Subscriptions Due for Delivery (" + count + ")");
					
				   var html = "";
  			      for (var i = 0; i < count; i ++) {
				    var entry = result[i];
					html += "<tr>";
					html += "<td class=\"col-sm-2 text-left\">" + entry.ID+ "</td>";
					
					html += "<td class=\"col-sm-6 text-left\">";
					html += "<b>" + entry.student_name + "</b><br>";
					html += "Class " + entry.class_name + ", " + entry.school_name;
					html += "</td>";
					html += "</tr>";
				     } 
				     $("#tblDueSubs> tbody").html("");
				     $("#tblDueSubs tbody").append(html);
				} else {
					$('#btnPrintDueSubs').hide();
				}

			} // function(data)
		});

   }
   
     function printLabels2(date) {
   		$('#lnkPrint').attr("href", data);
		$('#lnkPrint').hide();

   		var data = "date=" + date + "&subs=1";
  	 	$.ajax({
			 type: "GET",
			 url: "../ajax/print-slips-for-a-date.php",
			 data: data,
			 error: function (xhr, status, error) {
			 		hideLoader();
				 	alert(xhr +"," + status +"," + error);

			 },
			 success: function(data){
				$('#lnkPrint2').attr("href", data);
				$('#lnkPrint2').show();
				
			} // function(data)
		});

   }

