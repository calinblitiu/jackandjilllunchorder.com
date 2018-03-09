$(document).ready(function() {
	

 });

function doDel(id) {
	if (confirm("Permanently delete this student?")) {
		window.location=webRoot + "student-listing/del/" + id;
	}
}

function doPaging(p) {
	document.frmList.p.value = p;
	var sort = document.frmList.sort.value;
	if (sort == "")
		sort = "none";
	var name = document.frmList.xname.value;
	if (name == "")
		name = "none";
	var link = "/p/" + p + "/sort/" + sort + "/name/" + name;
	window.location = webRoot + "student-listing" + link;
}

function xvalidate(frm) {
	frm.p.value = "1";
	doPaging(frm.p.value);
	return false;
}

function setSort(sort) {
    document.frmList.sort.value = sort;
	var frm = document.frmList;
	frm.p.value = "1";
	doPaging(frm.p.value);

}
