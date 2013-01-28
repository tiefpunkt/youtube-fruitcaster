$(document).ready(function() {
	$("#" + window.location.hash.replace("#", "")).addClass("info");
});

window.onhashchange = function(){
	$("tr").removeClass("info");
	$("#" + window.location.hash.replace("#", "")).addClass("info");
};
