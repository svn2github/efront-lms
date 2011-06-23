if ($('module_administrator_tools_autocomplete_users_div')) {
	autocompleter = 
		new Ajax.Autocompleter("module_administrator_tools_autocomplete_users",
				"module_administrator_tools_autocomplete_users_div",
				"ask.php?ask_type=users", {paramName: "preffix",
			afterUpdateElement : function (t, li) {$('module_administrator_tools_users_LOGIN').value = li.id;},
			indicator : "module_administrator_tools_busy"});
}
if ($('module_administrator_tools_autocomplete_impersonate')) {
	autocompleter = 
		new Ajax.Autocompleter("autocomplete_impersonate",
				"module_administrator_tools_autocomplete_impersonate",
				"ask.php?ask_type=users", {paramName: "preffix",
			afterUpdateElement : function (t, li) {$('autocomplete_impersonate_user').value = li.id;},
			indicator : "module_administrator_tools_busy"});
}
function activate(el, action) {
	Element.extend(el);
	parameters = {ajax:1, method: 'get'};	
	el.down().className.match(/inactiveImage/) ? parameters = Object.extend(parameters, {activate:action}) : parameters = Object.extend(parameters, {deactivate:action}) ;
	el.down().setAttribute('src', 'themes/default/images/others/progress_big.gif');
	el.down().removeClassName('sprite32');
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onActivate);

}
function onActivate(el, response) {
	el.down().setAttribute('src', 'themes/default/images/others/transparent.gif');
	el.down().addClassName('sprite32');
	if (el.down().className.match(/inactiveImage/)) {
		el.down().removeClassName('inactiveImage');
	} else {
		el.down().addClassName('inactiveImage');
	}
	
	if (top.sideframe) {
		top.sideframe.location.reload();
	}
}

function ajaxPost(login, el, table_id) {
	var url = location.toString();
	var parameters = {postAjaxRequest:1, method: 'get'};
    if (login) {
        var userType = $('type_'+login).options[$('type_'+login).selectedIndex].value;
        Object.extend(parameters, {login: login, user_type: userType});
    } else if (table_id && table_id == 'usersTable') {
        el.checked ? Object.extend(parameters, {addAll: 1}) : Object.extend(parameters, {removeAll: 1});
        if ($(table_id+'_currentFilter')) {
        	Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
        }
    }
	ajaxRequest(el, url, parameters);
}

function removeUsersFromEntity(el) {
	var url = location.toString();
	var parameters = {remove_users_from_courses:1, method: 'get'};
	ajaxRequest(el, url, parameters);
}

function exportUsersToXls(el) {
	var parameters = {ajax:'xls', method:'get'}
	ajaxRequest(el, location.toString(), parameters, onExportUsersToXls);
}
function onExportUsersToXls(el, response) {
	$('popup_frame').src = location.toString()+'&ajax=show_xls';
}
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
function fixCase(el) {
	var parameters = {ajax:'fix_case', method:'get'}
	ajaxRequest(el, location.toString(), parameters);			
}
function onFixCase(el, response) {
	
}

if ($('module_administrator_tools_autocomplete_lessons_div')) { 
	new Ajax.Autocompleter("autocomplete", 
						   "module_administrator_tools_autocomplete_lessons_div", 
						   "ask.php?ask_type=lessons&course_only=1", {paramName: "preffix", 
											   afterUpdateElement : function (t, li) {document.location = document.location.toString().replace(/&tab=\w*/g, '').replace(/&lessons_ID=\d*/g, '')+"&tab=set_course_lesson_users"+"&lessons_ID="+li.id;}, 
											   indicator : "busy"}); 
}

if ($('module_administrator_tools_autocomplete_lessons_blockorder')) { 
	new Ajax.Autocompleter("autocomplete_blockorder", 
						   "module_administrator_tools_autocomplete_lessons_blockorder", 
						   "ask.php?ask_type=lessons", {paramName: "preffix", 
											   afterUpdateElement : function (t, li) {document.location = document.location.toString().replace(/&tab=\w*/g, '').replace(/&lessons_ID=\d*/g, '')+"&tab=global_settings"+"&lessons_ID="+li.id;},
											   indicator : "busy"}); 
}
