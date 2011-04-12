function updateBranchJobs(el) {
	var branch = el.options[el.options.selectedIndex].value;
	var parameters = {method:'get', ajax:1, jobs_for_branch:branch};
	ajaxRequest(el, location.toString(), parameters, onUpdateBranchJobs);
}
function onUpdateBranchJobs(el, response) {
	if (response.evalJSON(true).status) {
		$('jobs_for_branch').select('option').each(function (s) {s.remove();});
		$H(response.evalJSON(true).jobs).each(function (s) {$('jobs_for_branch').insert(new Element('option', {value:s[0]}).update(s[1]));});
	}
}
function deleteJob(el, job) {
	var parameters = {method:'get', ajax:1, delete_job:job};
	ajaxRequest(el, location.toString(), parameters, onDeleteJob);	
}
function onDeleteJob(el, response) {
	if (response.evalJSON(true).status) {
		new Effect.Fade(el.up().up());
	}	
}
function deleteHistory(el, event_id) {
    var url = location.toString();
    parameters = {delete_event:event_id, ajax:1, method: 'get'};		
	ajaxRequest(el, url, parameters, onDeleteHistory);
}
function onDeleteHistory(el, transport) {
	new Effect.Fade(el.up().up());
}
function deleteEvaluation(el, evaluation_id) {
    var url = location.toString();
    parameters = {delete_evaluation:evaluation_id, ajax:1, method: 'get'};		
	ajaxRequest(el, url, parameters, onDeleteEvaluation);
}
function onDeleteEvaluation(el, transport) {
	new Effect.Fade(el.up().up());
}
function addAccount(el) {
    parameters = {method: 'get', ajax: 'additional_accounts', login:$('account_login').value, pwd:$('account_password').value};
	ajaxRequest(el, location.toString(), parameters, onAddAccountSuccess);    
    
}
function onAddAccountSuccess(el, responseText) {
	if (top.sideframe) {
		top.sideframe.location.reload();
		$('additional_accounts').insert(new Element('tr').insert(new Element('td').update(login)).insert(new Element('td').insert(img)));
		if ($('empty_accounts')) {
		    $('empty_accounts').remove();
		}
		$('add_account').hide();
		var login = $('account_login').value;
		$('account_login').value    = '';
		$('account_password').value = '';
		el.removeClassName('sprite16-progress1').addClassName('sprite16-check2');
		
		var img = new Element('img').writeAttribute({src: 'themes/default/images/others/transparent.gif', alt:'', title:'', onclick:'deleteAccount(this, \''+login+'\')'}).addClassName('sprite16 sprite16-error_delete handle'); 
	} else {
		window.location = window.location.toString().replace(/op=\w+/, "op=mapped_accounts");
	}
}
function deleteAccount(el, login) {
 	parameters = {method: 'get', ajax: 'additional_accounts', login:login, 'delete':1};
 	var url    = location.toString();
	ajaxRequest(el, url, parameters, onDeleteAccountSuccess, onDeleteAccountFailure);    
}
function deleteFacebookAccount(el, login) {
 	parameters = {method: 'get', ajax: 'additional_accounts', fb_login:login, 'delete':1};
 	var url    = location.toString();
	ajaxRequest(el, url, parameters, onDeleteAccountSuccess, onDeleteAccountFailure);    
}
function onDeleteAccountFailure(el, responseText) {
	showMessage(responseText, 'failure');
	el.removeClassName('sprite16-progress1').addClassName('sprite16-delete');
}
function onDeleteAccountSuccess(el, responseText) {
	el.hide();
    new Effect.Fade(el.up().up());
	if (top.sideframe) {
		top.sideframe.location.reload();
	}
}
//Wrapper used from the select_all method
function ajaxPost(id, el, table_id) {
    if (table_id == 'skillsTable') {
    	ajaxUserSkillPost(id, el, table_id);
    } else if (table_id == 'lessonsTable') {
    	ajaxUserLessonPost(id, el, table_id);
    } else if (table_id == 'coursesTable') {
    	ajaxUserCoursePost(id, el, table_id);
    } else if (table_id == 'instancesTable') {
    	ajaxUserInstancePost(id, el, table_id);
    } else if (table_id == 'groupsTable'){
    	ajaxUserGroupPost(id, el, table_id);
    }
}
function ajaxUserSkillPost(id, el, table_id) {
	if ($('spec_skill_score_'+id) && ( isNaN(parseInt($('spec_skill_score_'+id).value)) || $('spec_skill_score_'+id).value > 100 || $('spec_skill_score_'+id).value < 1)) {
		return false;
	} else if (id) {
		parameters = {method:'get',add_skill:id, insert:$('skill_'+id).checked, specification:encodeURI($('spec_skill_'+id).value), score:encodeURI($('spec_skill_score_'+id).value)};
    } else if (table_id && table_id == 'skillsTable') {
    	parameters = {method:'get', add_skill:1};
        el.checked ? parameters = Object.extend(parameters, {addAll:1}) : Object.extend(parameters, {removeAll:1});
        if ($(table_id+'_currentFilter')) {
        	Object.extend(parameters, {filter:$(table_id+'_currentFilter').innerHTML});        	
        }
    }
	Object.extend(parameters, {postAjaxRequest:1});

	url=location.toString();
	ajaxRequest(el, url, parameters, onAjaxUserSkillPost);
}
function onAjaxUserSkillPost(el, response) {
	eF_js_redrawPage('skillsTable');
}
function ajaxUserGroupPost(id, el, table_id) {
    if (id) {
		parameters = {method:'get',add_group:id, insert:$('group_'+id).checked};
    } else if (table_id && table_id == ('groupsTable') ) {
    	parameters = {method:'get', add_skill:1};
        el.checked ? parameters = Object.extend(parameters, {addAll:1}) : Object.extend(parameters, {removeAll:1});
        if ($(table_id+'_currentFilter')) {
        	Object.extend(parameters, {filter:$(table_id+'_currentFilter').innerHTML});        	
        }
    }
	Object.extend(parameters, {postAjaxRequest:1});

	url=location.toString();
	ajaxRequest(el, url, parameters, onAjaxUserGroupPost);
}
function onAjaxUserGroupPost(el, response) {
	eF_js_redrawPage('groupsTable');
}
function ajaxUserCoursePost(id, el, table_id) {
	var baseUrl =  augmentUrl(table_id) + '&postAjaxRequest=1';
    if (id) {
        var url = baseUrl + '&add_course=' + id + '&insert=' + $('course_'+id).checked + '&user_type='+encodeURI($('course_type_'+id).value);
    } else if (table_id && table_id == ('coursesTable')) {
        el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
        if ($(table_id+'_currentFilter')) {
            url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
        }
        url += '&add_course=1';
    }
	ajaxRequest(el, url, {ajax:1}, onAjaxUserCoursePost);
}
function onAjaxUserCoursePost(el, response) {
	eF_js_redrawPage('coursesTable');
}
function ajaxUserInstancePost(id, el, table_id) {
	var baseUrl =  augmentUrl(table_id) + '&postAjaxRequest=1';
    if (id) {
        var url = baseUrl + '&add_course=' + id + '&tab=courses&insert=' + $('course_'+id).checked + '&user_type='+encodeURI($('course_type_'+id).value);
    } else if (table_id && table_id == ('instancesTable')) {
        el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
        if ($(table_id+'_currentFilter')) {
            url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
        }
        url += '&add_course=1&tab=courses';
    }
	ajaxRequest(el, url, {ajax:1}, onAjaxUserInstancePost);
}
function onAjaxUserInstancePost(el, response) {
	eF_js_redrawPage('instancesTable');
}
function ajaxUserLessonPost(id, el, table_id) {	
	var baseUrl =  augmentUrl(table_id) + '&postAjaxRequest=1';
	if (id) {
        var url = location.toString() + '&postAjaxRequest=1&add_lesson=' + id + '&insert=' + $('lesson_'+id).checked + '&user_type='+encodeURI($('lesson_type_'+id).value);
    } else if (table_id && table_id == ('lessonsTable') ) {
        el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
        if ($(table_id+'_currentFilter')) {
            url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
        }
        url += '&add_lesson=1';
    }    
	ajaxRequest(el, url, {ajax:1}, onAjaxUserLessonPost);
}
function onAjaxUserLessonPost(el, response) {
	eF_js_redrawPage('lessonsTable');
}
function toggleUserAccess(el, id, type) {
	parameters = {ajax:'toggle_user', type: type, id: id, method: 'get'};
	var url    = location.toString();

	ajaxRequest(el, url, parameters, onToggleUserAccess);		
}
function onToggleUserAccess(el, response) {
	if (response.evalJSON(true).status) {
		if (response.evalJSON(true).access == 1) {
			setImageSrc(el, 16, 'success');
		    el.writeAttribute({title:translationsToJS['_USERACCESSGRANTED'], alt:translationsToJS['_USERACCESSGRANTED']});	
		} else {
			setImageSrc(el, 16, 'warning');
		    el.writeAttribute({title:translationsToJS['_APPLICATIONPENDING'], alt:translationsToJS['_APPLICATIONPENDING']});	
		}
	} else {
		alert('Some problem emerged');
	}
}
function showFormAdditionalDetails(el, id) {
	Element.extend(el);
	$('form_tr_'+id).down().toggle();
	$('form_tr_'+id).down().visible() ? img = 'minus2' : img = 'plus2';
	setImageSrc(el, 16, img);	
}
function resetFormRows(el) {
	$$('tr.form_additional_info').each(function(s) {$(s.id+'_previous').insert({after:s.remove()});});
	setCookie("setUserFormSelectedSort", el.down().id+'--'+(el.down().hasClassName('sortAscending') ? 'asc' : 'desc'));
}
function showFormAdditionalDetails(el, id) {
	Element.extend(el);
	$('form_tr_'+id).down().toggle();
	$('form_tr_'+id).down().visible() ? img = 'minus2' : img = 'plus2';
	setImageSrc(el, 16, img);	
}
//social
var __initStatus;
var __noChangeEscape = 0;
function showStatusChange() {
    __initStatus = $('inputStatusText').value;
    $('statusText').hide();
    $('inputStatusText').show();//style.display = 'block';
    $('inputStatusText').focus();
}

function changeStatus() {
    if (__initStatus != $('inputStatusText').value) {
    	var url = location.toString()+"&postAjaxRequest=1&setStatus=" + $('inputStatusText').value;
        $('inputStatusText').hide();

        if ($('inputStatusText').value != '') {
            $('statusText').innerHTML = "\"<i>" + $('inputStatusText').value + "</i>\"";
            if (top.sideframe && top.sideframe.$('statusText')) {   // for default theme this is called from sidebar.js
            	top.sideframe.$('statusText').innerHTML = $('inputStatusText').value;
            	top.sideframe.$('inputStatusText').value = $('inputStatusText').value;
            }
        } else {
            $('statusText').innerHTML = "<i>[" + translations['clicktochange'] + "]</i>";
            if (top.sideframe && top.sideframe.$('statusText')) {
            	top.sideframe.$('statusText').innerHTML = "[" + translations['clicktochange'] + "]";
            	top.sideframe.$('inputStatusText').value = "";
         	}
        }
        $('statusText').show();
        
        parameters = {method: 'get'};
        ajaxRequest($('statusTextProgressImg'), url, parameters);
        
    } else {
        $('inputStatusText').hide();
        $('statusText').show();
    }
    __noChangeEscape = 0;
}
function ExpandCollapseFormRows() {
	setFormRowsHidden = parseInt(readCookie("setFormRowsHidden"));
	if (!setFormRowsHidden) {
		$$('tr.form_additional_info').each(function(s) {s.down().show();setImageSrc($(s.id+'_previous').down().down(), 16, 'minus2');});
		setFormRowsHidden = 1;
	} else {
		$$('tr.form_additional_info').each(function(s) {s.down().hide();setImageSrc($(s.id+'_previous').down().down(), 16, 'plus2');});
		setFormRowsHidden = 0;
	}
	setCookie("setFormRowsHidden", setFormRowsHidden);
}
ExpandCollapseFormRows();ExpandCollapseFormRows();	//2 calls in order to set the expand status to the correct state (because 0 calls does nothing, 1 call reverts it)

readCookieForSortedTablePreset = 'setUserFormSelectedSort';


