function deleteRule(el, id) {
	parameters = {delete_rule:id, method: 'get'};
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onDeleteEntity);	
}
function onDeleteEntity(el, response) {
	new Effect.Fade(el.up().up());
}
function deleteCondition(el, id) {
	parameters = {delete_condition:id, method: 'get'};
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onDeleteEntity);	
}
function onDeleteEntity(el, response) {
	new Effect.Fade(el.up().up());
}


function selectRule(el) {
	Element.extend(el);
	$('rule_unit').hide();
	$('test_unit').hide();
	$('test_score').hide();

	if (el.options[el.selectedIndex].value == 'hasnot_seen') {
		$('rule_unit').show();
	} else if (el.options[el.selectedIndex].value == 'hasnot_passed') {
		$('test_unit').show();
		$('test_score').show();
	}
}

function selectCondition(el) {
    $('percentage_units').hide();
    $('specific_unit').hide();
    //$('all_tests').hide();
    $('specific_test').hide();
    $('time_in_lesson').hide();
    //$('specific_test_score').hide();

    switch (el.options[el.selectedIndex].value) {
        case 'percentage_units' :
            $('percentage_units').show();
            break;
        case 'specific_unit' :
            $('specific_unit').show();
            break;
        case 'all_tests' :
            //$('all_tests').show();
            break;
        case 'specific_test' :
            $('specific_test').show();
            //$('specific_test_score').show();
            break;
        case 'time_in_lesson' :
            $('time_in_lesson').show();
            break;
        default:
            break;
    }
}
function setAutoComplete(el) {
	Element.extend(el);
	el.insert(new Element('img', {src:'themes/default/images/others/progress1.gif'}).addClassName('handle'));

	parameters = {action:'auto_complete', method: 'get'};
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onSetAutoComplete);	
}
function onSetAutoComplete(el, response) {
	if (response == 0) {
		el.update(autocompleteno);
	} else {
		el.update(autocompleteyes);
	}	
}
