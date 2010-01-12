function activateUserType(el, user_type) {
	if (el.className.match('red')) {
    	parameters = {activate_user_type:user_type, method: 'get'};
	} else {
		parameters = {deactivate_user_type:user_type, method: 'get'};
	}
    var url    = 'administrator.php?ctg=user_types';
    ajaxRequest(el, url, parameters, onActivateUserType);
}
function onActivateUserType(el, response) {
    if (response == 0) {
    	setImageSrc(el, 16, "trafficlight_red.png");
        el.writeAttribute({alt:activate, title:activate});
    } else if (response == 1) {
    	setImageSrc(el, 16, "trafficlight_green.png");
        el.writeAttribute({alt:deactivate, title:deactivate});
    }
}

function deleteUserType(el, user_type) {
	parameters = {delete_user_type:user_type, method: 'get'};
	var url    = 'administrator.php?ctg=user_types';
	ajaxRequest(el, url, parameters, onDeleteUserType);	
}
function onDeleteUserType(el, response) {
	new Effect.Fade(el.up().up());
}