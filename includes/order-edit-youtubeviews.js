$(document).ready(function() {
	
	$('#btnCancel').click(function () {
			window.location = "orders.php";
			
	});
		
	$('#cboRefererType').change(function() {
			if ($(this).val() == "0") {
				$('#colUserRefererList').hide();
			} else if ($(this).val() == "1") {
				$('#colUserRefererList').show();
			}
	})

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

	$('#suggestedAT').click(function() {
		$('#divSuggestedMT').hide();
	});
	$('#suggestedMT').click(function() {
		$('#divSuggestedMT').show();
	});
	
	$('#suggestedMTVideos').click(function() {
		$('#divSuggestedMTVideoIds').show();
		$('#divSuggestedMTChannelIds').hide();
	});

	$('#suggestedMTChannels').click(function() {
		$('#divSuggestedMTVideoIds').hide();
		$('#divSuggestedMTChannelIds').show();
	});


	$('#externalAT').click(function() {
		$('#divExternalMT').hide();
	});
	$('#externalMT').click(function() {
		$('#divExternalMT').show();
	});

	$('#searchAT').click(function() {
		$('#divSearchMT').hide();
	});
	$('#searchMT').click(function() {
		$('#divSearchMT').show();
	});

	$('#playlistAT').click(function() {
		$('#divPlaylistMT').hide();
	});
	$('#playlistMT').click(function() {
		$('#divPlaylistMT').show();
	});

	$('#otherChannelsAT').click(function() {
		$('#divOtherChannelsMT').hide();
	});
	$('#otherChannelsMT').click(function() {
		$('#divOtherChannelsMT').show();
	});


  });
  
   function xvalidate() {
 
	var message = "<ul>";
	var retVal = true;

	var frm = document.frmOrder;
	var url = frm.url.value;
	var max_views= frm.max_views.value;
	var view_min_time = frm.view_min_time.value;
	var view_max_time = frm.view_max_time.value;
	var max_hours_to_take = frm.cboMaxHours.value;
	var proxy = frm.cboProxies.value;
	
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
	
	var suggestedP = frm.suggestedP.value;
	var externalP = frm.externalP.value;
	var searchP = frm.searchP.value;
	var playlistP = frm.playlistP.value;
	var otherChannelsP = frm.otherchannelsP.value;
	var browseP = frm.browseP.value;
	
	if (parseInt(proxy) == 0) {
		message += "<li>A Proxy Provider must be selected</li>";
		retVal = false;
	}	
	var totalP = parseInt(suggestedP) + parseInt(externalP) + parseInt(searchP) + parseInt(playlistP) + parseInt(otherChannelsP) + parseInt(browseP);
	if (isNaN(totalP) || parseInt(totalP) != 100) {
		message += "<li>Referer Percentages should equal 100%</li>";
		retVal = false;
	}
	var suggestedATMT = "";
	if (frm.suggestedAT.checked)
			suggestedATMT = "AT";
	else if (frm.suggestedMT.checked)
			suggestedATMT = "MT";
	var suggested_mt_visit_min_time = frm.suggested_mt_visit_min_time.value;
	var suggested_mt_visit_max_time = frm.suggested_mt_visit_max_time.value;
	
	var suggestedMTIds = "";
	if (frm.suggestedMTVideos.checked)
		suggestedMTIds = "VIDEO";
	else if (frm.suggestedMTChannels.checked)
		suggestedMTIds = "CHANNEL";
	var suggestedMTVideoIds = frm.suggestedMTVideoIds.value;
	var suggestedMTChannelIds = frm.suggestedMTChannelIds.value;
	var suggested_mt_max_videos_per_channel = frm.suggested_mt_max_videos_per_channel.value;
	
	if (suggestedATMT == "MT") {
		if (suggested_mt_visit_min_time == "" || suggested_mt_visit_min_time.indexOf(".") > -1 || isNaN(suggested_mt_visit_min_time)) {
		message += "<li>Suggested MT Visit Min time is invalid</li>";
		retVal = false;
		}
		if (suggested_mt_visit_max_time == "" || suggested_mt_visit_max_time.indexOf(".") > -1 || isNaN(suggested_mt_visit_max_time)) {
			message += "<li>Suggested MT Visit Max time is invalid</li>";
			retVal = false;
		}
	
		if (parseInt(suggested_mt_visit_max_time) <= parseInt(suggested_mt_visit_min_time)) {
			message += "<li>Suggested MT Visit Max time should be more than Visit Min Time</li>";
			retVal = false;
		}
		
		if (suggestedMTIds == "VIDEO") {
			if (suggestedMTVideoIds == null || suggestedMTVideoIds.trim() == "") {
				message += "<li>You have not given a list of Video ids for Suggested MT</li>";
				retVal = false;
			}
		}
		else if (suggestedMTIds == "CHANNEL") {
			if (suggestedMTChannelIds == null || suggestedMTChannelIds.trim() == "") {
				message += "<li>You have not given a list of Channel ids for Suggested MT</li>";
				retVal = false;
			}
			if (suggested_mt_max_videos_per_channel == "" || suggested_mt_max_videos_per_channel.indexOf(".") > -1 || isNaN(suggested_mt_max_videos_per_channel)) {
				message += "<li>Suggested MT Max Videos per Channel is invalid</li>";
				retVal = false;
			}
		}	
		
	}
	
	
	
	var externalATMT = "";
	if (frm.externalAT.checked)
		externalATMT = "AT";
	else if (frm.externalMT.checked)
		externalATMT = "MT";
	var external_mt_visit_min_time = frm.external_mt_visit_min_time.value;
	var external_mt_visit_max_time = frm.external_mt_visit_max_time.value;
	var externalMTReferers = frm.externalMTReferers.value;
	if (externalATMT == "MT") {
		if (external_mt_visit_min_time == "" || external_mt_visit_min_time.indexOf(".") > -1 || isNaN(external_mt_visit_min_time)) {
		message += "<li>External MT Visit Min time is invalid</li>";
		retVal = false;
		}
		if (external_mt_visit_max_time == "" || external_mt_visit_max_time.indexOf(".") > -1 || isNaN(external_mt_visit_max_time)) {
			message += "<li>External MT Visit Max time is invalid</li>";
			retVal = false;
		}
	
		if (parseInt(external_mt_visit_max_time) <= parseInt(external_mt_visit_min_time)) {
			message += "<li>External MT Visit Max time should be more than Visit Min Time</li>";
			retVal = false;
		}
		if (externalMTReferers == null || externalMTReferers.trim() == "") {
			message += "<li>You have not given a Referrer List for External MT</li>";
			retVal = false;
		}
		var arr = externalMTReferers.split('\n');
		var wrong = "";
		
		for(var i = 0; i < arr.length; i++) {
			if (!isValidURL(arr[i]))
				wrong += arr[i] + ",";
		}
		if (wrong != "") {
			message += "<li>These Referrer URL(s) look invalid in External MT: " + wrong + "</li>";
			retVal = false;
		}
	}
	
	var searchATMT = "";
	if (frm.searchAT.checked)
		searchATMT = "AT";
	else if (frm.searchMT.checked)
		searchATMT = "MT";
	var searchMTKeywords = frm.searchMTKeywords.value;
	if (searchATMT == "MT") {
		if (searchMTKeywords == null || searchMTKeywords.trim() == "") {
			message += "<li>You have not given a keyword list for Search MT</li>";
			retVal = false;
		}	
	}
	
	
	var playlistATMT = "";
	if (frm.playlistAT.checked)
		playlistATMT = "AT";
	else if (frm.playlistMT.checked)
		playlistATMT = "MT";
	var playlistMTIds = frm.playlistMTIds.value;
	if (playlistATMT == "MT") {
		if (playlistMTIds == null || playlistMTIds.trim() == "") {
			message += "<li>You have not given a  Playlist list for Playlist MT</li>";
			retVal = false;
		}	
	}
	
	var otherChannelsATMT = "";
	if (frm.otherChannelsAT.checked)
		otherChannelsATMT = "AT";
	else if (frm.otherChannelsMT.checked)
		otherChannelsATMT = "MT";
	var otherChannelsChannelIds = frm.otherChannelsMTIds.value;
	if (otherChannelsATMT == "MT") {
		if (otherChannelsChannelIds == null || otherChannelsChannelIds.trim() == "") {
			message += "<li>You have not given a  Channel list for Other Channels MT</li>";
			retVal = false;
		}	
	}		

	if (!isValidURL(url)) {
		message += "<li>URL is invalid</li>";
		retVal = false;
	}
	if (max_views == "" || max_views.indexOf(".") > -1 || isNaN(max_views)) {
		message += "<li>Max Views is invalid</li>";
		retVal = false;
	}
	if (view_min_time == "" || view_min_time.indexOf(".") > -1 || isNaN(view_min_time)) {
		message += "<li>View Min time is invalid</li>";
		retVal = false;
	}
	if (view_max_time == "" || view_max_time.indexOf(".") > -1 || isNaN(view_max_time)) {
		message += "<li>View Max time is invalid</li>";
		retVal = false;
	}
	
	if (parseInt(view_max_time) <= parseInt(view_min_time)) {
		message += "<li>View Max time should be more than Visit Min Time</li>";
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

