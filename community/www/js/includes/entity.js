function deleteEntity(el, id) {
	parameters = {'delete':id, method: 'get'};
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onDeleteEntity);	
}
function onDeleteEntity(el, response) {
	new Effect.Fade(el.up().up());
}
