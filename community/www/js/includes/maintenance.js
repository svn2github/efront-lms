function reIndex(el) {
	var parameters = {ajax:1, reindex:1, method:'get'};
    var url    = location.toString();
    ajaxRequest(el, url, parameters, onReIndex);
}
function onReIndex(el, response) {
//	alert(reindexcomplete);
}
function clearCache(el, cache) {
	var parameters = {ajax:1, cache:cache, method:'get'};
    var url    = location.toString();
    ajaxRequest(el, url, parameters);
}

