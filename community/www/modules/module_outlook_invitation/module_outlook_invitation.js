function deleteEvent(el, id) {
	parameters = {delete_event:id, method: 'get'};
	var url = location.toString();
	ajaxRequest(el, url, parameters, onDeleteEvent);	
}
function onDeleteEvent(el, response) {
	//new Effect.Fade(el.up().up());
	try {
		eF_js_changePage(0, 0);
	} catch (e) {alert(e);}
}

if ($('set_schedules_link_0')) {
	onSetSchedule_orig = onSetSchedule;
	onSetSchedule = function(el, response) {
		if (el.id == 'set_schedules_link_0') {
			if (confirm('Would you like to send an outlook invitation now?')) {
				var url = window.location.protocol+'//'+window.location.host+window.location.pathname+'?ctg=module&op=module_outlook_invitation&course='+window.location.search.match(/course=(\d+)/)[1]+'&add_event=1&popup=1';
				$('popup_frame').src = url;
				eF_js_showDivPopup('Send invitation', 3);
			}
		}
		onSetSchedule_orig(el, response);
	}
}