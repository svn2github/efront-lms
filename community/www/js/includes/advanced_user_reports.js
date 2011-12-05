function setSpecification(el) {
	Element.extend(el);
	selected = el.options[el.options.selectedIndex].value;
	$$('tr.specification').each(function(s) {s.hide();});
	if ($('specification_'+selected) && isNaN(parseInt(selected))) {
		$('specification_'+selected).show();
	}
}

function additionalSpecificationChanged(el, specification) {
	Element.extend(el);
	selected = el.options[el.options.selectedIndex].value;

	if (specification == 'timestamp' || specification == 'last_login' || specification == 'hired_on' || specification == 'left_on') {
		if (selected == 'between') {
			$(specification+'_first_date').show();
			$(specification+'_second_date').show();
		} else {
			$(specification+'_first_date').hide();
			$(specification+'_second_date').hide();
		}
	}
}

function deleteCondition(el, condition) {
	parameters = {'delete_condition':condition, method: 'get'};
	ajaxRequest(el, location.toString(), parameters, onDeleteCondition);		
}
function onDeleteCondition(el, response) {
	new Effect.Fade(el.up().up());
}

function deleteColumn(el, column) {
	parameters = {'delete_column':column, method: 'get'};
	ajaxRequest(el, location.toString(), parameters, onDeleteColumn);		
}
function onDeleteColumn(el, response) {
	new Effect.Fade(el.up().up());
}

function deleteReport(el, report) {
	parameters = {'delete_report':report, method: 'get'};
	ajaxRequest(el, location.toString(), parameters, onDeleteReport);			
}
function onDeleteReport(el, response) {
	if (response.evalJSON(true) && response.evalJSON(true).status) {
		if (window.location.toString().match("&report="+response.evalJSON(true).delete_report)) {
			window.location = window.location.toString().replace(/&report=\d*/, '').replace(/&tab=\w*/, '')+'&tab=builder';
		} else {
			$('reports_list_edit').select('option').each(function (s) {if (s.value==response.evalJSON(true).delete_report) {s.remove();}});
			$('delete_report').hide();
		}
	}
}

function saveColumnTree(el) {
	parameters = {'order':treeObj.getNodeOrders(), ajax:1, method: 'get'};
	ajaxRequest(el, location.toString(), parameters);	
}
function saveConditionTree(el) {
	parameters = {'order':treeObj.getNodeOrders(), ajax:1, method: 'get'};
	ajaxRequest(el, location.toString(), parameters, onSaveConditionTree);	
}
function onSaveConditionTree(el, response) {
    parent.eF_js_redrawPage('conditionsTable', true);
}
function setAlign(el, column) {
	parameters = {'set_align':column, ajax:1, method: 'get'};
	ajaxRequest(el, location.toString(), parameters, onSetAlign);			
}
function onSetAlign(el, response) {
	if (response.evalJSON(true).status) {
		if (response.evalJSON(true).align == 'left') {
			Element.extend(el).previous().update(translations['left']);
		} else if (response.evalJSON(true).align == 'center') {
			Element.extend(el).previous().update(translations['center']);
		} else if (response.evalJSON(true).align == 'right') {
			Element.extend(el).previous().update(translations['right']);
		}
	}
}
function setDefaultSort(el, column) {
	parameters = {'default_sort':column, ajax:1, method: 'get'};
	ajaxRequest(el, location.toString(), parameters, onSetDefaultSort);		
}
function onSetDefaultSort(el, response) {
	$('columns_table').select('img.sprite16-pin_green').each(function (s) {setImageSrc(s, 16, 'pin_red.png');});
    setImageSrc(el, 16, 'pin_green.png');
}

function setConditionStatus(el, condition) {
	parameters = {'set_condition_status':condition, ajax:1, method: 'get'};
	ajaxRequest(el, location.toString(), parameters, onSetStatus);		
}
function setColumnStatus(el, condition) {
	parameters = {'set_column_status':condition, ajax:1, method: 'get'};
	ajaxRequest(el, location.toString(), parameters, onSetStatus);		
}
function onSetStatus(el, response) {	
	if (response.evalJSON(true).active == 1) {
		setImageSrc(el, 16, 'trafficlight_green');
		el.previous().update(1);
	} else if (response.evalJSON(true).active == 0) {
		setImageSrc(el, 16, 'trafficlight_red');
		el.previous().update(0);
	}
}

function exportCsv(el) {	
	eF_js_redrawPage('usersTable', false, 'csv');    
    currentOther = new Array();   
    
    $('popup_frame').src = location.toString()+'&ajax=1&csv=1';
}
function exportXls(el) {	
	eF_js_redrawPage('usersTable', false, 'xls');    
    currentOther = new Array();   
    
    $('popup_frame').src = location.toString()+'&ajax=1&xls=1';
}
function applyOperation(el, operation) {
	parameters = {'operation':operation, ajax:1, method: 'get'};
	if (operation == 'group' || operation == 'course' || operation == 'lesson') {
		if ($(operation+'_options')) {
			var options = $(operation+'_options').options[$(operation+'_options').options.selectedIndex].value;
		} else {
			var options = '';
		}	
		if ($(operation+'_selected')) {
			var selected = $(operation+'_selected').options[$(operation+'_selected').options.selectedIndex].value;
		} else {
			var selected = '';
		}	
		if ($(operation+'_user_role')) {
			var user_role = $(operation+'_user_role').options[$(operation+'_user_role').options.selectedIndex].value;
		} else {
			var user_role = 0;
		}
		
		additional_parameters = {'options':options, 'selected':selected, 'user_role': user_role};
		Object.extend(parameters, additional_parameters);
		if (operation == 'group') {
			Object.extend(parameters, {'new_group':$('new_group').value});
		}
	}	
	ajaxRequest(el, location.toString(), parameters, onApplyOperation);			
}
function onApplyOperation(el, response) {
	if (response.evalJSON(true).table_name) {
		eF_js_showDivPopup('', '', response.evalJSON(true).table_name);
	}
	eF_js_redrawPage('usersTable', true);
}

function ajaxPost(foo, el) {
	toggleUserDynamicGroup(el, true);
}

function toggleUserDynamicGroup(el, allUsers) {
	user = el.id.replace('check_', '');
	if (allUsers) {
		if (el.checked) {
			eF_js_redrawPage('usersTable', false, 'dynamic');
			currentOther = new Array();
		} else {
			parameters = {toggle_user_to_dynamic_group:1, status:0, allUsers:1, ajax:1, method: 'get'};
			ajaxRequest(el, location.toString(), parameters);
		}
	} else {
		parameters = {toggle_user_to_dynamic_group:user, status:el.checked, ajax:1, method: 'get'};
		ajaxRequest(el, location.toString(), parameters);
	}
	
}
/*
function removeFromSet(el, user) {
	parameters = {remove_user_from_dynamic_group:1, user:user, ajax:1, method: 'get'};
	ajaxRequest(el, location.toString(), parameters, onRemoveFromSet);
}
function onRemoveFromSet(el, response) {
	if (response.evalJSON(true).status) {
		new Effect.Fade(el.up().up());
	}
}
*/
function onFinishedAddingConditions() {
	eF_js_showDivPopup('', '');
	eF_js_redrawPage('conditionsTable', true);
}

if (typeof(finishedAddingConditions) != 'undefined' && finishedAddingConditions) {
	parent.onFinishedAddingConditions();
}
