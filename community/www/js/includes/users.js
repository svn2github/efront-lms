function activateUser(el, user) {
	if (el.className.match('red')) {
    	parameters = {activate_user:user, method: 'get'};
	} else {
		parameters = {deactivate_user:user, method: 'get'};
	}
    var url    = location.toString();
    ajaxRequest(el, url, parameters, onActivateUser);
}
function onActivateUser(el, response) {
    if (response == 0) {
    	setImageSrc(el, 16, "trafficlight_red.png");
        el.writeAttribute({alt:activate, title:activate});
        el.up().up().addClassName('deactivatedTableElement');
    } else if (response == 1) {
    	setImageSrc(el, 16, "trafficlight_green.png");
        el.writeAttribute({alt:deactivate, title:deactivate});
        el.up().up().removeClassName('deactivatedTableElement');
    }
}

function deleteUser(el, user) {
	parameters = {delete_user:user, method: 'get'};	
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onDeleteUser);	
}
function onDeleteUser(el, response) {
	new Effect.Fade(el.up().up());
}
function archiveUser(el, user) {
	parameters = {archive_user:user, method: 'get'};	
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onArchiveUser);	
}
function onArchiveUser(el, response) {
	new Effect.Fade(el.up().up());
}
