function activateLanguage(el, language) {
	if (el.className.match('red')) {
    	parameters = {activate_language:language, method: 'get'};
	} else {
		parameters = {deactivate_language:language, method: 'get'};
	}
    var url    = 'administrator.php?ctg=languages';
    ajaxRequest(el, url, parameters, onActivateLanguage);
}
function onActivateLanguage(el, response) {
    if (response == 0) {
    	setImageSrc(el, 16, "trafficlight_red.png");
        el.writeAttribute({alt:activate, title:activate});
    } else if (response == 1) {
    	setImageSrc(el, 16, "trafficlight_green.png");
        el.writeAttribute({alt:deactivate, title:deactivate});
    }
}

function deleteLanguage(el, language) {
	parameters = {delete_language:language, method: 'get'};
	var url    = 'administrator.php?ctg=languages';
	ajaxRequest(el, url, parameters, onDeleteLanguage);	
}
function onDeleteLanguage(el, response) {
	new Effect.Fade(el.up().up());
}