	$(document).ready(function() {
	
		$('#btnCancel').click(function () {
			window.location = "orders.php";
			
		});
		$('#cboRefererType').change(function() {
			if ($(this).val() == "SYSTEM") {
				$('#colUserRefererList').hide();
			} else if ($(this).val() == "USER") {
				$('#colUserRefererList').show();
			}
		})

		if ($('#cboRefererType').val() == "SYSTEM") {
				$('#colUserRefererList').hide();
		} else if ($('#cboRefererType').val() == "USER") {
				$('#colUserRefererList').show();
		}
			
		$('#cboMaxHours').change(function() {
			if ($(this).val() == "0") {
				$('#divMaxVisitsPerProxyIP').hide();
			} else  {
				$('#divMaxVisitsPerProxyIP').show();
			}
		})
		if ($('#cboMaxHours').val() == "0") {
				$('#divMaxVisitsPerProxyIP').hide();
		} else  {
				$('#divMaxVisitsPerProxyIP').show();
		}
			
		$('#lstBrowsers[multiple]').multiselect({
			columns : 1,  
			selectAll: true,
			minHeight: 200,
			maxHeight: null
			
		});
		$('#lstPlatforms[multiple]').multiselect({
			columns : 1,  
			selectAll: true,
			minHeight: 200,
			maxHeight: null
			
		});
		
		
   });

   function xvalidate() {
 
	var message = "<ul>";
	var retVal = true;

	var frm = document.frmOrder;
	var url = frm.url.value;
	var max_visits = frm.max_visits.value;
	var visit_min_time = frm.visit_min_time.value;
	var visit_max_time = frm.visit_max_time.value;
	var max_hours_to_take = frm.cboMaxHours.value;
	var max_visits_per_proxy_ip = frm.cboMaxVisitsPerProxyIP.value;
	var proxy = frm.cboProxies.value;
	var url2 = frm.url2.value;
	
	var browsers = "";
	for(var i = 0; i < frm.lstBrowsers.options.length; i++){
        if(frm.lstBrowsers.options[i].selected)
            browsers += frm.lstBrowsers.options[i].value + ",";
    }
	
	frm.browsers.value = browsers;
    
    var platforms ="";
	for(var i = 0; i < frm.lstPlatforms.options.length; i++){
        if(frm.lstPlatforms.options[i].selected)
            platforms += frm.lstPlatforms.options[i].value + ",";
    }
	frm.platforms.value = platforms;
	
	var referer_type = frm.cboRefererType.value;
	var referer_list = "";
	
	if (referer_type == "USER")
		referer_list = frm.userreferers.value;

	if (!isValidURL(url)) {
		message += "<li>URL is invalid</li>";
		retVal = false;
	}

	if (!isValidURL(url2)) {
		message += "<li>Second URL is invalid</li>";
		retVal = false;
	}	
	if (url != "" && url == url2) {
		message += "<li>Second URL cannot be same as main URL</li>";
		retVal = false;
	}
	if (max_visits == "" || max_visits.indexOf(".") > -1 || isNaN(max_visits)) {
		message += "<li>Max Visits is invalid</li>";
		retVal = false;
	}
	if (visit_min_time == "" || visit_min_time.indexOf(".") > -1 || isNaN(visit_min_time)) {
		message += "<li>Visit Min time is invalid</li>";
		retVal = false;
	}
	if (visit_max_time == "" || visit_max_time.indexOf(".") > -1 || isNaN(visit_max_time)) {
		message += "<li>Visit Max time is invalid</li>";
		retVal = false;
	}
	
	if (parseInt(visit_max_time) <= parseInt(visit_min_time)) {
		message += "<li>Visit Max time should be more than Visit Min Time</li>";
		retVal = false;
	}
	
	if (referer_type == "1") {
		if (referer_list == null || referer_list.trim() == "") {
			message += "<li>You have not given a Referrer List</li>";
			retVal = false;
		}
		var arr = referer_list.split('\n');
		var wrong = "";
		
		for(var i = 0; i < arr.length; i++) {
			if (!isValidURL(arr[i]))
				wrong += arr[i] + ",";
		}
		if (wrong != "") {
			message += "<li>These Referrer URL(s) look invalid:" + wrong + "</li>";
			retVal = false;
		}
		
	}
	if (parseInt(proxy) == 0) {
		message += "<li>A Proxy Provider must be selected</li>";
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

function isValidURL(str) {
  var regex = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;
  if(!regex .test(str)) {
    return false;
  } else {
    return true;
  }
}