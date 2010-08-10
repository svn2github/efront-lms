function setSpecification(el) {
 Element.extend(el);
 selected = el.options[el.options.selectedIndex].value;
 $$('tr.specification').each(function(s) {s.hide();});
 if ($('specification_'+selected)) {
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
 window.location = window.location.toString().replace(/&report=\d*/, '');
}

function saveColumnTree(el) {
 parameters = {'order':treeObj.getNodeOrders(), ajax:1, method: 'get'};
 ajaxRequest(el, location.toString(), parameters);
}

function setDefaultSort(el, column) {
 parameters = {'default_sort':column, ajax:1, method: 'get'};
 ajaxRequest(el, location.toString(), parameters, onSetDefaultSort);
}
function onSetDefaultSort(el, response) {
 $('columns_table').select('img.sprite16-pin_green').each(function (s) {setImageSrc(s, 16, 'pin_red.png');});
    setImageSrc(el, 16, 'pin_green.png');
/*
    tables = sortedTables.size();
    for (var i = 0; i < tables; i++) {
        if (sortedTables[i].id.match('usersTable')) {
            eF_js_rebuildTable(i, 0, 'null', 'desc');
        }
    }
*/
}

function setStatus(el, condition) {
 parameters = {'set_status':condition, ajax:1, method: 'get'};
 ajaxRequest(el, location.toString(), parameters, onSetStatus);
}
function onSetStatus(el, response) {
 if (response.evalJSON(true).active == 1) {
  setImageSrc(el, 16, 'trafficlight_green');
 } else if (response.evalJSON(true).active == 0) {
  setImageSrc(el, 16, 'trafficlight_red');
 }
}

//Uses the 'other' argument in eF_js_rebuildTable to send the action
function exportCsv(el) {
    tables = sortedTables.size();

    for (var i = 0; i < tables; i++) {
        if (sortedTables[i].id.match('usersTable')) {
            eF_js_rebuildTable(i, 0, 'null', 'desc', 'csv');
        }
    }

    currentOther = new Array();

    $('popup_frame').src = location.toString()+'&ajax=1&csv=1';
    //parameters = {'csv':1, ajax:1, method: 'get'};
    //ajaxRequest(el, location.toString(), parameters, onExportCsv);
}
/*
function onExportCsv(el, response) {
	$('popup_frame').src = 'view_file.php?file='+response.evalJSON(true).path;
}
*/
function applyOperation(el, operation) {
 parameters = {'operation':operation, ajax:1, method: 'get'};
 if (operation == 'group' || operation == 'course' || operation == 'lesson') {
  additional_parameters = {'options':$(operation+'_options').options[$(operation+'_options').options.selectedIndex].value, 'selected':$(operation+'_selected').options[$(operation+'_selected').options.selectedIndex].value};
  Object.extend(parameters, additional_parameters);
  if (operation == 'group') {
   Object.extend(parameters, {'new_group':$('new_group').value});
  }
 }
 ajaxRequest(el, location.toString(), parameters, onApplyOperation);
}
function onApplyOperation(el, response) {
    eF_js_showDivPopup('', '', response.evalJSON(true).table_name);

    tables = sortedTables.size();
    for (var i = 0; i < tables; i++) {
        if (sortedTables[i].id.match('usersTable')) {
            eF_js_rebuildTable(i, 0, 'null', 'desc');
        }
    }
}
