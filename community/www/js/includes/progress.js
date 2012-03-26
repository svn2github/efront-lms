function resetProgressInLesson(el, login) {
	var url = location.toString();
	var parameters = {reset_user:login, method: 'get'};
	ajaxRequest(el, url, parameters, onResetProgressInLesson);
}
function onResetProgressInLesson(el, response) {
	eF_js_redrawPage('usersTable', true);
}
function changeProgressInLesson(el, login, date) {
	var url = location.toString();
	var parameters = {change_user:login, date:date, method: 'get'};
	ajaxRequest(el, url, parameters, onResetProgressInLesson);
}
function onChangeProgressInLesson(el, response) {
	eF_js_redrawPage('usersTable', true);
}
function completeSelected(el, tableId, date) {	
	entities = new Array();
	$(tableId).select("input[type=checkbox]").each(function (s) {		
		if (s.checked && s.id) {
			entities.push(s.value);
		}
	});
	parameters = {'complete':entities.toJSON(), date:date, method: 'get'};
	ajaxRequest(el, url, parameters, oncompleteSelected);	
}
function uncompleteSelected(el, tableId) {
	entities = new Array();
	$(tableId).select("input[type=checkbox]").each(function (s) {
		if (s.checked && s.id) {
			entities.push(s.value);
		}
	});
	parameters = {'uncomplete':entities.toJSON(), method: 'get'};
	ajaxRequest(el, url, parameters, oncompleteSelected);	
}
function oncompleteSelected(el, response) {
	eF_js_redrawPage('usersTable', true);	
	$('all_status_id').hide();
}

function initialization() {	
	$$('input.datepicker').each(function (s) {init_date_picker(s.id)}); 
}
document.observe("dom:loaded", initialization);
onSortedTableComplete = initialization;