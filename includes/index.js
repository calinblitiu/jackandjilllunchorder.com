
$(document).ready(function () {


})



function xvalidate(frm) {

 var search = frm.search.value;
 if (search == null || search == "")
 	return false;

 window.location = webRoot + "products-list/search/" + escape(search) + "/sort/name_asc";
 return false;
}


