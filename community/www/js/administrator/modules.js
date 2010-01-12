function activateModule(el, module) {
	if (el.src.match('red')) {
    	parameters = {activate_module:module, method: 'get'};
	} else {
		parameters = {deactivate_module:module, method: 'get'};
	}
    var url    = 'administrator.php?ctg=modules';
    ajaxRequest(el, url, parameters, onActivateModule);
}
function onActivateModule(el, response) {
    if (response == 0) {
        el.writeAttribute({src:"images/16x16/trafficlight_red.png", 
            			   alt:"<?php echo _ACTIVATE?>",
            			   title:"<?php echo _ACTIVATE?>"});
    } else if (response == 1) {
        el.writeAttribute({src:"images/16x16/trafficlight_green.png", 
						   alt:"<?php echo _DEACTIVATE?>",
						   title:"<?php echo _DEACTIVATE?>"});
    }
}

function deleteModule(el, module) {
	parameters = {delete_module:module, method: 'get'};
	var url    = 'administrator.php?ctg=modules';
	ajaxRequest(el, url, parameters, onDeleteModule);	
}
function onDeleteModule(el, response) {
	new Effect.Fade(el.up().up());
}
