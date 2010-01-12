function deleteComment(el, id) {
	parameters = {'delete':id, method: 'get'};
	var url    = location.toString().match('ctg=content') ? location.toString().replace('ctg=content', 'ctg=comments') : location.toString()+'&ctg=comments';
	ajaxRequest(el, url, parameters, onDeleteComment);	
}
function onDeleteComment(el, response) {
	new Effect.Fade(el.up().up());
}
