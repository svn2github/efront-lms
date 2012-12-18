function deleteUser(el, user) {
	parameters = {delete_user:user, method: 'get'};	
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onDeleteUser);	
}
function onDeleteUser(el, response) {
	new Effect.Fade(el.up().up());
}
function archiveUser(el, user) {
	parameters = {archive_user:user, method: 'get'};	
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onArchiveUser);	
}
function onArchiveUser(el, response) {
	new Effect.Fade(el.up().up());
}
function setPeriod(el) {
	Element.extend(el);
	//alert(el.value);
	var dates = el.value.split("|");
	var from_date = dates[0].split(",");
	var to_date = dates[1].split(",");

	if (from_date[0] < 10) {
		$('"from_Month"').value  = "0" + from_date[0];
	} else {
		$('"from_Month"').value  = from_date[0];
	}
	$('"from_Day"').value    = from_date[1];
	$('from_Year').value = from_date[2];

	if (to_date[0] < 10) {
		$('"to_Month"').value  = "0" + to_date[0];
	} else {
		$('"to_Month"').value  = to_date[0];
	}
	$('"to_Day"').value    = to_date[1];
	$('to_Year').value = to_date[2];               	
}
function showStats(interval) {
	var fromDate = new Date();				
	var toDate   = new Date();
	if (interval == 'day') {
		fromDate.setDate(fromDate.getDate() - 1);
		toDate.setDate(toDate.getDate());
	} else if (interval == 'week') {
		fromDate.setDate(fromDate.getDate() - 7);
		toDate.setDate(toDate.getDate());
	} else if (interval == 'month') {
		fromDate.setDate(fromDate.getDate() - 30);
		toDate.setDate(toDate.getDate());
	}
	document.period.from_Day.options[fromDate.getDate()-1].selected = 'selected';
	document.period.to_Day.options[toDate.getDate()-1].selected = 'selected';
	document.period.from_Month.options[fromDate.getMonth()].selected = 'selected';
	document.period.to_Month.options[toDate.getMonth()].selected = 'selected';
	for (var i = 0; i < document.period.from_Year.options.length; i++) {
		if (document.period.from_Year.options.value == fromDate.getFullYear()) {
			document.period.from_Year.options[i].selected = 'selected';
		}
	}
	for (var i = 0; i < document.period.to_Year.options.length; i++) {
		if (document.period.to_Year.options.value == toDate.getFullYear()) {
			document.period.to_Year.options[i].selected = 'selected';
		}
	}					
} 

function resetTest(el, id) {
	Element.extend(el);
	url = 'view_test.php?done_test_id='+id+'&reset=1';
    el.down().src = 'images/others/progress1.gif';
    new Ajax.Request(url, {
            method:'get',
            asynchronous:true,
            encoding: 'UTF-8',
            onFailure: function (transport) {
                el.down().writeAttribute({src:'images/16x16/error_delete.png', title:transport.responseText}).hide();
                new Effect.Appear(el.down().identify());
                window.setTimeout('Effect.Fade("'+el.down().identify()+'")', 10000);
            },
            onSuccess: function (transport) {
            	new Effect.Fade(el.up().up());
            }
    });                    	
}

function showSystemStats(interval) {
	var fromDate = new Date();				
	var toDate   = new Date();
	if (interval == 'day') {
		fromDate.setDate(fromDate.getDate() - 1);
		toDate.setDate(toDate.getDate());
	} else if (interval == 'week') {
		fromDate.setDate(fromDate.getDate() - 7);
		toDate.setDate(toDate.getDate());
	} else if (interval == 'month') {
		fromDate.setDate(fromDate.getDate() - 30);
		toDate.setDate(toDate.getDate());
	}
	document.systemperiod.from_Day.options[fromDate.getDate()-1].selected = 'selected';
	document.systemperiod.to_Day.options[toDate.getDate()-1].selected = 'selected';
	document.systemperiod.from_Month.options[fromDate.getMonth()].selected = 'selected';
	document.systemperiod.to_Month.options[toDate.getMonth()].selected = 'selected';
	for (var i = 0; i < document.systemperiod.from_Year.options.length; i++) {	
		if (document.systemperiod.from_Year.options[i].value == fromDate.getFullYear()) {
			document.systemperiod.from_Year.options[i].selected = 'selected';
		}
	}
	for (var i = 0; i < document.systemperiod.to_Year.options.length; i++) {
		if (document.systemperiod.to_Year.options[i].value == toDate.getFullYear()) {
			document.systemperiod.to_Year.options[i].selected = 'selected';
		}
	}					
} 

function showCustomStats(interval) {
	var fromDate = new Date();	
	var toDate   = new Date();
	if (interval == 'day') {
		fromDate.setDate(fromDate.getDate() - 1);
		toDate.setDate(toDate.getDate());
	} else if (interval == 'week') {
		fromDate.setDate(fromDate.getDate() - 7);
		toDate.setDate(toDate.getDate());
	} else if (interval == 'month') {
		fromDate.setDate(fromDate.getDate() - 30);
		toDate.setDate(toDate.getDate());
	}
		document.lesson_enrolled.from_Day.options[fromDate.getDate()-1].selected = 'selected';
		document.course_enrolled.from_Day.options[fromDate.getDate()-1].selected = 'selected';
		document.lesson_completed.from_Day.options[fromDate.getDate()-1].selected = 'selected';
		document.course_completed.from_Day.options[fromDate.getDate()-1].selected = 'selected';
		document.course_certificated.from_Day.options[fromDate.getDate()-1].selected = 'selected';
		document.project_submitted.from_Day.options[fromDate.getDate()-1].selected = 'selected';
		document.test_completed.from_Day.options[fromDate.getDate()-1].selected = 'selected';
		document.active_users.from_Day.options[fromDate.getDate()-1].selected = 'selected';
		document.active_lessons.from_Day.options[fromDate.getDate()-1].selected = 'selected';
		document.system_registered.from_Day.options[fromDate.getDate()-1].selected = 'selected';
		

		document.lesson_enrolled.to_Day.options[toDate.getDate()-1].selected = 'selected';
		document.course_enrolled.to_Day.options[toDate.getDate()-1].selected = 'selected';
		document.lesson_completed.to_Day.options[toDate.getDate()-1].selected = 'selected';
		document.course_completed.to_Day.options[toDate.getDate()-1].selected = 'selected';
		document.course_certificated.to_Day.options[toDate.getDate()-1].selected = 'selected';
		document.project_submitted.to_Day.options[toDate.getDate()-1].selected = 'selected';
		document.test_completed.to_Day.options[toDate.getDate()-1].selected = 'selected';
		document.active_users.to_Day.options[toDate.getDate()-1].selected = 'selected';
		document.active_lessons.to_Day.options[toDate.getDate()-1].selected = 'selected';
		document.system_registered.to_Day.options[toDate.getDate()-1].selected = 'selected';

		document.lesson_enrolled.from_Month.options[fromDate.getMonth()].selected = 'selected';
		document.course_enrolled.from_Month.options[fromDate.getMonth()].selected = 'selected';
		document.lesson_completed.from_Month.options[fromDate.getMonth()].selected = 'selected';
		document.course_completed.from_Month.options[fromDate.getMonth()].selected = 'selected';
		document.course_certificated.from_Month.options[fromDate.getMonth()].selected = 'selected';
		document.project_submitted.from_Month.options[fromDate.getMonth()].selected = 'selected';
		document.test_completed.from_Month.options[fromDate.getMonth()].selected = 'selected';
		document.active_users.from_Month.options[fromDate.getMonth()].selected = 'selected';
		document.active_lessons.from_Month.options[fromDate.getMonth()].selected = 'selected';
		document.system_registered.from_Month.options[fromDate.getMonth()].selected = 'selected';					


		document.lesson_enrolled.to_Month.options[toDate.getMonth()].selected = 'selected';
		document.course_enrolled.to_Month.options[toDate.getMonth()].selected = 'selected';
		document.lesson_completed.to_Month.options[toDate.getMonth()].selected = 'selected';
		document.course_completed.to_Month.options[toDate.getMonth()].selected = 'selected';
		document.course_certificated.to_Month.options[toDate.getMonth()].selected = 'selected';
		document.project_submitted.to_Month.options[toDate.getMonth()].selected = 'selected';
		document.test_completed.to_Month.options[toDate.getMonth()].selected = 'selected';
		document.active_users.to_Month.options[toDate.getMonth()].selected = 'selected';
		document.active_lessons.to_Month.options[toDate.getMonth()].selected = 'selected';
		document.system_registered.to_Month.options[toDate.getMonth()].selected = 'selected';	
		
		
		for (var i = 0; i < document.lesson_enrolled.from_Year.options.length; i++) {
			if (document.lesson_enrolled.from_Year.options[i].value == fromDate.getFullYear()) {
				document.lesson_enrolled.from_Year.options[i].selected = 'selected';
				document.course_enrolled.from_Year.options[i].selected = 'selected';
				document.lesson_completed.from_Year.options[i].selected = 'selected';
				document.course_completed.from_Year.options[i].selected = 'selected';
				document.course_certificated.from_Year.options[i].selected = 'selected';
				document.project_submitted.from_Year.options[i].selected = 'selected';
				document.test_completed.from_Year.options[i].selected = 'selected';
				document.active_users.from_Year.options[i].selected = 'selected';
				document.active_lessons.from_Year.options[i].selected = 'selected';
				document.system_registered.from_Year.options[i].selected = 'selected';
			}
		}
		for (var i = 0; i < document.lesson_enrolled.to_Year.options.length; i++) {
			if (document.lesson_enrolled.to_Year.options[i].value == toDate.getFullYear()) {
				document.lesson_enrolled.to_Year.options[i].selected = 'selected';
				document.course_enrolled.to_Year.options[i].selected = 'selected';
				document.lesson_completed.to_Year.options[i].selected = 'selected';
				document.course_completed.to_Year.options[i].selected = 'selected';
				document.course_certificated.to_Year.options[i].selected = 'selected';
				document.project_submitted.to_Year.options[i].selected = 'selected';
				document.test_completed.to_Year.options[i].selected = 'selected';
				document.active_users.to_Year.options[i].selected = 'selected';
				document.active_lessons.to_Year.options[i].selected = 'selected';
				document.system_registered.to_Year.options[i].selected = 'selected';
			}
		}

}
function showCertificateStats(interval) {
	var fromDate = new Date();	
	var toDate   = new Date();
	if (interval == 'day') {
		fromDate.setDate(fromDate.getDate() - 1);
		toDate.setDate(toDate.getDate());
	} else if (interval == 'week') {
		fromDate.setDate(fromDate.getDate() - 7);
		toDate.setDate(toDate.getDate());
	} else if (interval == 'month') {
		fromDate.setDate(fromDate.getDate() - 30);
		toDate.setDate(toDate.getDate());
	}
	document.course_certificated.from_Day.options[fromDate.getDate()-1].selected = 'selected';
	document.course_certificated.to_Day.options[toDate.getDate()-1].selected = 'selected';
	document.course_certificated.from_Month.options[fromDate.getMonth()].selected = 'selected'; 
	document.course_certificated.to_Month.options[toDate.getMonth()].selected = 'selected';
	
	document.course_certificated_all.from_Day.options[fromDate.getDate()-1].selected = 'selected';
	document.course_certificated_all.to_Day.options[toDate.getDate()-1].selected = 'selected';
	document.course_certificated_all.from_Month.options[fromDate.getMonth()].selected = 'selected'; 
	document.course_certificated_all.to_Month.options[toDate.getMonth()].selected = 'selected';
	for (var i = 0; i < document.course_certificated.from_Year.options.length; i++) {
		if (document.course_certificated.from_Year.options[i].value == fromDate.getFullYear()) {
			document.course_certificated.from_Year.options[i].selected = 'selected';
			document.course_certificated_all.from_Year.options[i].selected = 'selected';
		}
	}
	for (var i = 0; i < document.course_certificated.to_Year.options.length; i++) {
		if (document.course_certificated.to_Year.options[i].value == toDate.getFullYear()) {
			document.course_certificated.to_Year.options[i].selected = 'selected';
			document.course_certificated_all.to_Year.options[i].selected = 'selected';
		}
	}	
}
function showParticipationStats(interval) {
	var fromDate = new Date();	
	var toDate   = new Date();
	if (interval == 'day') {
		fromDate.setDate(fromDate.getDate() - 1);
		toDate.setDate(toDate.getDate());
	} else if (interval == 'week') {
		fromDate.setDate(fromDate.getDate() - 7);
		toDate.setDate(toDate.getDate());
	} else if (interval == 'month') {
		fromDate.setDate(fromDate.getDate() - 30);
		toDate.setDate(toDate.getDate());
	}
	document.participation_statistics_form.from_Day.options[fromDate.getDate()-1].selected = 'selected';
	document.participation_statistics_form.to_Day.options[toDate.getDate()-1].selected = 'selected';
	document.participation_statistics_form.from_Month.options[fromDate.getMonth()].selected = 'selected'; 
	document.participation_statistics_form.to_Month.options[toDate.getMonth()].selected = 'selected';
	

	for (var i = 0; i < document.participation_statistics_form.from_Year.options.length; i++) {
		if (document.participation_statistics_form.from_Year.options[i].value == fromDate.getFullYear()) {
			document.participation_statistics_form.from_Year.options[i].selected = 'selected';
		}
	}
	for (var i = 0; i < document.participation_statistics_form.to_Year.options.length; i++) {
		if (document.participation_statistics_form.to_Year.options[i].value == toDate.getFullYear()) {
			document.participation_statistics_form.to_Year.options[i].selected = 'selected';
		}
	}	
}




function refreshEventResults(action)
{
    // Update all form tables
    var tables = sortedTables.size();

//	var fromDate = new Date();	
//	var toDate   = new Date();
	
//	fromDate.setMonth(document.event_statistics_form.from_Month.value, document.event_statistics_form.from_Day.value);
//	fromDate.setFullYear(document.event_statistics_form.from_Year.value);
		
//	toDate.setMonth(document.event_statistics_form.to_Month.value, document.event_statistics_form.to_Day.value);
//	toDate.setFullYear(document.event_statistics_form.to_Year.value);
 
	from = document.event_statistics_form.from_Day.value + "_" + document.event_statistics_form.from_Month.value + "_" + document.event_statistics_form.from_Year.value;
	to = document.event_statistics_form.to_Day.value + "_" + document.event_statistics_form.to_Month.value + "_" + document.event_statistics_form.to_Year.value;
 
	var event_type_value = "";
	
	var eventRow = $('eventTypesTable').down().down().next();
	while (eventRow) {
		eventTypeColumn = eventRow.down().next();
		if (eventTypeColumn) {
			eventCheckbox = eventTypeColumn.down().next();
			if (eventCheckbox && eventCheckbox.checked) {
				eventName = eventCheckbox.id;
				
				if (event_type_value == "") {
					event_type_value = eventName.substr(6); 	
				} else {
					event_type_value += "_" + eventName.substr(6);
				}
				
			}
			
		}
		eventRow = eventRow.next();
	}
	
	var url = "administrator.php?ctg=statistics&option=events&users_login=" + $('event_user_login').value + "&lessons_ID=" + $('event_lesson_id').value + "&courses_ID=" + $('event_course_id').value + "&event_type=" + event_type_value + "&from=" + from + "&to=" + to + "&";
	if (action == "excel") {
		location = url + "excel=events";		
	} else if (action == "pdf") {
		location = url + "pdf=events";
	} else {
	    for (i = 0; i < tables; i++) {
	        if (sortedTables[i].id == 'foundEvents') {
		        ajaxUrl[i] = url; 
	            eF_js_rebuildTable(i, 0, 'null', 'desc');
	        }
	    }
	}
}
function showEventStats(interval) {
	var fromDate = new Date();				
	var toDate   = new Date();
	if (interval == 'day') {
		fromDate.setDate(fromDate.getDate() - 1);
		toDate.setDate(toDate.getDate());
	} else if (interval == 'week') {
		fromDate.setDate(fromDate.getDate() - 7);
		toDate.setDate(toDate.getDate());
	} else if (interval == 'month') {
		fromDate.setDate(fromDate.getDate() - 30);
		toDate.setDate(toDate.getDate());
	}
	document.event_statistics_form.from_Day.options[fromDate.getDate()-1].selected = 'selected';
	document.event_statistics_form.to_Day.options[toDate.getDate()-1].selected = 'selected';
	document.event_statistics_form.from_Month.options[fromDate.getMonth()].selected = 'selected';
	document.event_statistics_form.to_Month.options[toDate.getMonth()].selected = 'selected';
	for (var i = 0; i < document.event_statistics_form.from_Year.options.length; i++) {
		if (document.event_statistics_form.from_Year.options.value == fromDate.getFullYear()) {
			document.event_statistics_form.from_Year.options[i].selected = 'selected';
		}
	}
	for (var i = 0; i < document.event_statistics_form.to_Year.options.length; i++) {
		if (document.event_statistics_form.to_Year.options.value == toDate.getFullYear()) {
			document.event_statistics_form.to_Year.options[i].selected = 'selected';
		}
	}
	
	refreshEventResults();
}	

function showOnlyForUsers(el, table, option) {
    // Update all form tables
    var tables = sortedTables.size();
    
	var url = "administrator.php?ctg=statistics&option="+option+"&sel_group=" + selGroup + "&";
	if (el.checked) {
		url += "only_my_users=1&"; 	
	}
		
	for (i = 0; i < tables; i++) {
        if (sortedTables[i].id == table) {
	        ajaxUrl[i] = url; 
            eF_js_rebuildTable(i, 0, 'null', 'desc');
        }
    }
		
}
function showsearchtype(type) {
$('group_row').hide();
$('user_type_row').hide();
$('lesson_row').hide();
$('course_row').hide();
$(type+'_row').show();

}

function appendSelection (ob) {
	selected = new Array(); 
	for (var i = 0; i < $(ob).options.length; i++) {
		if ($(ob).options[ i ].selected) {
			selected.push($(ob).options[ i ].value);
		}
	}
	return selected.toString();
}

if ($('autocomplete_users')) { 
	new Ajax.Autocompleter("autocomplete", 
						   "autocomplete_users", 
						   "ask.php?ask_type=users", {paramName: "preffix", 
													afterUpdateElement : function (t, li) {document.location=document.location.toString().replace(/&sel_user=\w*/, '')+'&sel_user='+li.id;}, 
													indicator : "busy"}); 
}
if ($('autocomplete_lessons')) { 
	new Ajax.Autocompleter("autocomplete", 
						   "autocomplete_lessons", 
						   "ask.php?ask_type=lessons", {paramName: "preffix", 
											   afterUpdateElement : function (t, li) {document.location=document.location+'&sel_lesson='+li.id;}, 
											   indicator : "busy"}); 
}
if ($('autocomplete_courses')) { 
	new Ajax.Autocompleter("autocomplete", 
						   "autocomplete_courses", 
						   "ask.php?ask_type=courses", {paramName: "preffix",
											   afterUpdateElement : function (t, li) {document.location=document.location+'&sel_course='+li.id;}, 
											   indicator : "busy"}); 
}
if ($('autocomplete_tests')) { 
	new Ajax.Autocompleter("autocomplete", 
						   "autocomplete_tests", 
						   "ask.php?ask_type=tests", {paramName: "preffix",
											 afterUpdateElement : function (t, li) {document.location=document.location+'&sel_test='+li.id;}, 
											 indicator : "busy"}); 
}
if ($('autocomplete_feedback')) { 
	new Ajax.Autocompleter("autocomplete", 
						   "autocomplete_feedback", 
						   "ask.php?ask_type=feedback", {paramName: "preffix",
											 afterUpdateElement : function (t, li) {document.location=document.location+'&sel_test='+li.id;}, 
											 indicator : "busy"}); 
}
if ($('lesson_choices_enrolled')) { 
	new Ajax.Autocompleter("autocomplete_lesson_enrolled", 
						   "lesson_choices_enrolled", 
						   "ask.php?ask_type=lessons", {paramName: "preffix",
											   afterUpdateElement : function (t, li) {$('lesson_id_enrolled').value=li.id;}, 
											   indicator : "busy_lesson_enrolled"}); 
}
if ($('course_choices_enrolled')) { 
	new Ajax.Autocompleter("autocomplete_course_enrolled", 
						   "course_choices_enrolled", 
						   "ask.php?ask_type=courses&instances=1", {paramName: "preffix",
											   afterUpdateElement : function (t, li) {$('course_id_enrolled').value=li.id;}, 
											   indicator : "busy_course_enrolled"}); 
}
if ($('lesson_choices_completed')) { 
	new Ajax.Autocompleter("autocomplete_lesson_completed", 
						   "lesson_choices_completed", 
						   "ask.php?ask_type=lessons", {paramName: "preffix",
											   afterUpdateElement : function (t, li) {$('lesson_id_completed').value=li.id;}, 
											   indicator : "busy_lesson_completed"}); 
}
if ($('course_choices_completed')) { 
	new Ajax.Autocompleter("autocomplete_course_completed", 
						   "course_choices_completed", 
						   "ask.php?ask_type=courses&instances=1", {paramName: "preffix",
											   afterUpdateElement : function (t, li) {$('course_id_completed').value=li.id;}, 
											   indicator : "busy_course_completed"}); 
}
if ($('lesson_choices_participation')) { 
	new Ajax.Autocompleter("autocomplete_lesson_participation", 
						   "lesson_choices_participation", 
						   "ask.php?ask_type=lessons", {paramName: "preffix",
											   afterUpdateElement : function (t, li) {$('lesson_id_participation').value=li.id;}, 
											   indicator : "busy_lesson_participation"}); 
}
if ($('course_choices_participation')) {	
	new Ajax.Autocompleter("autocomplete_course_participation", 
						   "course_choices_participation", 
						   "ask.php?ask_type=courses", {paramName: "preffix",
											   afterUpdateElement : function (t, li) {$('course_id_participation').value=li.id;}, 
											   indicator : "busy_course_participation"}); 
}
if ($('course_choices_certificated')) { 
	new Ajax.Autocompleter("autocomplete_course_certificated", 
						   "course_choices_certificated", 
						   "ask.php?ask_type=courses", {paramName: "preffix",
											   afterUpdateElement : function (t, li) {$('course_id_certificated').value=li.id;}, 
											   indicator : "busy_course_certificated"}); 
}
if ($('project_choices_submitted')) { 
	new Ajax.Autocompleter("autocomplete_project_submitted", 
						   "project_choices_submitted", 
						   "ask.php?ask_type=projects", {paramName: "preffix",
											    afterUpdateElement : function (t, li) {$('project_id_submitted').value=li.id;}, 
											    indicator : "busy_project_submitted"}); 
}
if ($('test_choices_completed')) { 
	new Ajax.Autocompleter("autocomplete_test_completed", 
						   "test_choices_completed", 
						   "ask.php?ask_type=tests", {paramName: "preffix",
											 afterUpdateElement : function (t, li) {$('test_id_completed').value=li.id;}, 
											 indicator : "busy_test_completed"}); 
}

if ($('autocomplete_event_users')) { 
	new Ajax.Autocompleter("autocomplete", 
						   "autocomplete_event_users", 
						   "ask.php?ask_type=users", {paramName: "preffix", 
													afterUpdateElement : function (t, li) {$('event_user_login').value=li.id; $('autocomplete_event_users').value=li.id;refreshEventResults(); }, 
													indicator : "busy"}); 
}

if ($('autocomplete_event_lessons')) { 
	new Ajax.Autocompleter("autocomplete_lessons_ev", 
						   "autocomplete_event_lessons", 
						   "ask.php?ask_type=lessons", {paramName: "preffix",
											   afterUpdateElement : function (t, li) {$('event_lesson_id').value=li.id; $('autocomplete_event_lessons').value=li.id;refreshEventResults();}, 
											   indicator : "busy_event_lesson"}); 
}
if ($('autocomplete_event_courses')) { 
	new Ajax.Autocompleter("autocomplete_courses_ev", 
						   "autocomplete_event_courses", 
						   "ask.php?ask_type=courses", {paramName: "preffix",
											   afterUpdateElement : function (t, li) {$('event_course_id').value=li.id; $('autocomplete_event_courses').value=li.id;refreshEventResults();}, 
											   indicator : "busy_event_course"}); 
}
if ($('autocomplete_groups')) { 
	new Ajax.Autocompleter("autocomplete", 
						   "autocomplete_groups", 
						   "ask.php?ask_type=groups", {paramName: "preffix", 
													afterUpdateElement : function (t, li) {document.location=document.location+'&sel_group='+li.id;}, 
													indicator : "busy"}); 
}

if ($('autocomplete_branches')) { 
	new Ajax.Autocompleter("autocomplete", 
						   "autocomplete_branches", 
						   "ask.php?ask_type=branches", {paramName: "preffix", 
													afterUpdateElement : function (t, li) {document.location=document.location+'&sel_branch='+li.id;}, 
													indicator : "busy"}); 
}
if ($('autocomplete_skills')) { 
	new Ajax.Autocompleter("autocomplete", 
						   "autocomplete_skills", 
						   "ask.php?ask_type=skills", {paramName: "preffix", 
													afterUpdateElement : function (t, li) {document.location=document.location+'&sel_skill='+li.id;}, 
													indicator : "busy"}); 
}

