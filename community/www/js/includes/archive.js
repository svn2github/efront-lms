function deleteArchive(el, id) {
	parameters = {'delete':id, method: 'get'};
	ajaxRequest(el, url, parameters, onDeleteArchive);	
}
function onDeleteArchive(el, response) {
	new Effect.Fade(el.up().up());
}
function restoreArchive(el, id) {
	parameters = {'restore':id, method: 'get'};
	ajaxRequest(el, url, parameters, onRestoreArchive);	
}
function onRestoreArchive(el, response) {
	new Effect.Fade(el.up().up());
}


function deleteSelected(tableId) {
	$(tableId).select("input[type=checkbox]").each(function (s) {
		if (s.checked && s.id) {
			s.up().previous().select("img").each (function (p) {if (p.className.match(/delete/)) {deleteArchive(p, s.value);}});
		}
	});
}
function restoreSelected(tableId) {
	$(tableId).select("input[type=checkbox]").each(function (s) {
		if (s.checked && s.id) {
			s.up().previous().select("img").each (function (p) {if (p.className.match(/delete/)) {restoreArchive(p, s.value);}});
		}
	});
}
