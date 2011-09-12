function toggleAutoComplete(selection) {
	if (selection != 'global' && selection != 'private') {
		$('autocomplete').disabled = false;
		$('autocomplete').removeClassName('inactiveElement');
		switch (selection) {
			case 'course': 
				autocompleter.url = "ask.php?ask_type=courses";
				ajaxRequest($('autocomplete'), location.toString(), {set_default_course:1, method:'get'}, onSetDefaultValue);
			break;
			case 'lesson': 
				autocompleter.url = "ask.php?ask_type=lessons";  
				ajaxRequest($('autocomplete'), location.toString(), {set_default_lesson:1, method:'get'}, onSetDefaultValue);
			break;
			case 'group' : 
				autocompleter.url = "ask.php?ask_type=groups";   
				ajaxRequest($('autocomplete'), location.toString(), {set_default_group:1, method:'get'}, onSetDefaultValue);
			break;
			case 'branch':
			case 'sub_branch':
				autocompleter.url = "ask.php?ask_type=branches"; 
				ajaxRequest($('autocomplete'), location.toString(), {set_default_branch:1, method:'get'}, onSetDefaultValue);
			break;			
		}
		
	} else {
		$('autocomplete').disabled = true;
		$('autocomplete').addClassName('inactiveElement');
	}
	$('autocomplete').value = '';
	$('foreign_ID').value   = '';
}
function onSetDefaultValue(el, response) {
	if (response.evalJSON(true)) {
		el.value = response.evalJSON(true).name;
		$('foreign_ID').value = response.evalJSON(true).foreign_ID;
	}
}
function toggleAutoCompleteStatus() {	
	selection = $('select_type').options[$('select_type').options.selectedIndex].value;
	if (selection != 'global' && selection != 'private') {
		$('autocomplete').disabled = false;
		$('autocomplete').removeClassName('inactiveElement');
	} else {
		$('autocomplete').disabled = true;
		$('autocomplete').addClassName('inactiveElement');
	}
}

if ($('autocomplete_calendar')) {
	autocompleter = 
	new Ajax.Autocompleter("autocomplete",
                           "autocomplete_calendar",
                           "ask.php?ask_type="+$('select_type').options[$('select_type').options.selectedIndex].value, {paramName: "preffix",
                                               afterUpdateElement : function (t, li) {$('foreign_ID').value = li.id;},
                                               indicator : "busy"});
	toggleAutoCompleteStatus();	
}