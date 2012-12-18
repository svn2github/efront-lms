function archiveUser(el, user) {
	var parameters = {archive_user:user, ajax:'ajax', method:'get'}
	ajaxRequest(el, location.toString(), parameters, onArchiveUser);	
}
function onArchiveUser(el, response) {
	new Effect.Fade(el.up().up());
}
function archiveAllIdleUsers(el) {
	var parameters = {archive_all_users:1, ajax:'ajax', method:'get'}
	ajaxRequest(el, location.toString(), parameters, onArchiveAllIdleUsers);		
}
function onArchiveAllIdleUsers(el, response) {
	eF_js_redrawPage('idleUsersTable', true);
}
function toggleUser(el, user) {
	var parameters = {toggle_user:user, ajax:'ajax', method:'get'}
	ajaxRequest(el, location.toString(), parameters, onToggleUser);	
}
function onToggleUser(el, response) {
	if (response.evalJSON(true).active) {
		setImageSrc(el, 16, 'trafficlight_green');
		el.up().up().removeClassName('deactivatedTableElement');
	} else {
		setImageSrc(el, 16, 'trafficlight_red');
		el.up().up().addClassName('deactivatedTableElement');
	}
}
function deactivateAllIdleUsers(el) {
	var parameters = {deactivate_all_users:1, ajax:'ajax', method:'get'}
	ajaxRequest(el, location.toString(), parameters, onDeactivateAllIdleUsers);		
}
function onDeactivateAllIdleUsers(el, response) {
	eF_js_redrawPage('idleUsersTable', true);
}
function setFormDate(year, month, day) {
	$$('select').each(function(s) {
		if (s.name == 'idle_from_timestamp[d]') {
			s.options.selectedIndex = day-1;
		} else if (s.name == 'idle_from_timestamp[M]') {
			s.options.selectedIndex = month-1;
		} else if (s.name == 'idle_from_timestamp[Y]') {
			s.options.selectedIndex = year-2005;
		}
	});
}

function idle_export_excel(el) {
	Element.extend(el);
	var parameters = {excel:1, ajax:'ajax', method:'get'}
	ajaxRequest(el, location.toString(), parameters);	
		
}