function activateLesson(el, lesson) {
	if (el.className.match('red')) {
    	parameters = {activate_lesson:lesson, method: 'get'};
	} else {
		parameters = {deactivate_lesson:lesson, method: 'get'};
	}
    var url    = location.toString();
    ajaxRequest(el, url, parameters, onActivateLesson);
}
function onActivateLesson(el, response) {
    if (response == 0) {
    	setImageSrc(el, 16, "trafficlight_red.png");
        el.writeAttribute({alt:activate, title:activate});
        el.up().up().addClassName('deactivatedTableElement');
    } else if (response == 1) {
    	setImageSrc(el, 16, "trafficlight_green.png");
        el.writeAttribute({alt:deactivate, title:deactivate});
        el.up().up().removeClassName('deactivatedTableElement');
    }
}

function deleteLesson(el, lesson) {
	parameters = {delete_lesson:lesson, method: 'get'};
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onDeleteLesson);	
}
function onDeleteLesson(el, response) {
	new Effect.Fade(el.up().up());
}

function setLessonAccess(el, lesson) {
	if (el.className.match('courses')) {
    	parameters = {unset_course_only:lesson, method: 'get'};
	} else {
		parameters = {set_course_only:lesson, method: 'get'};
	}
    var url    = location.toString();
    ajaxRequest(el, url, parameters, onSetLessonAccess);
}
function onSetLessonAccess(el, response) {
    if (response == 0) {
    	setImageSrc(el, 16, "lessons.png");
        el.writeAttribute({alt:directly, title:directly});
    } else if (response == 1) {
    	setImageSrc(el, 16, "courses.png");
        el.writeAttribute({alt:courseonly, title:courseonly});
    }
}

function ajaxPost(id, el, table_id) {
	
    //Since in the same page there are 2 ajax post lists, we create a "wrapper" which decides which one to call
	if (table_id == 'branchesTable') {
		ajaxLessonBranchPost(id, el, table_id);	
	} else {
		table_id == 'skillsTable' ? ajaxLessonSkillUserPost(1, id, el, table_id) : usersAjaxPost(id, el, table_id);
	}
}

function usersAjaxPost(login, el, table_id) {
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
	ajaxRequest(el, url, parameters, false, undoCheck);	
    
}

function undoCheck(el, response) {
	el.checked ? el.checked = false : el.checked = true;
	alert(decodeURIComponent(response));
}

// type: 1 - inserting/deleting the skill to an employee | 2 - changing the specification
// id: the users_login of the employee to get the skill
// el: the element of the form corresponding to that skill/lesson
// table_id: the id of the ajax-enabled table
function ajaxLessonSkillUserPost(type, id, el, table_id) {
	var url = location.toString();
	var parameters = {postAjaxRequest:1, method: 'get'};

    if (type == 1) {
        if (id) {
            //var url = baseUrl + '&add_skill=' + id + '&insert='+el.checked + '&specification='+encodeURI(document.getElementById('spec_skill_'+id).value);
        	Object.extend(parameters, {add_skill: id, insert: el.checked, specification: encodeURI(document.getElementById('spec_skill_'+id).value)});
            //var img_id   = 'img_'+ id;
        } else if (table_id && table_id == 'skillsTable') {
            //el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
            el.checked ? Object.extend(parameters, {add_skill:1, addAll: 1}) : Object.extend(parameters, {add_skill:1, removeAll: 1});
            //url += '&add_skill=1';
            if ($(table_id+'_currentFilter')) {
            	Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
                //url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
            }
            //var img_id   = 'img_selectAll';
        }
    } else if (type == 2) {
        if (id) {
            //var url = baseUrl + '&add_skill=' + id + '&insert=true&specification='+el.value;
            Object.extend(parameters, {add_skill: id, insert: true, specification: el.value});
            //var img_id   = 'img_'+ id;
        }
    } else {
        return false;
    }
	ajaxRequest(el, url, parameters);	

}

// Used to associate lessons and branches
function ajaxLessonBranchPost(id, el, table_id) {
	var url = location.toString();
	var parameters = {postAjaxRequest:1, method: 'get'};

    if (id) {
    	Object.extend(parameters, {add_branch: id, insert: el.checked});
    } else if (table_id && table_id == 'branchesTable') {
        el.checked ? Object.extend(parameters, {add_branch:1, addAll: 1}) : Object.extend(parameters, {add_branch:1, removeAll: 1});
        if ($(table_id+'_currentFilter')) {
        	Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
        }
    }
 
	ajaxRequest(el, url, parameters);	

}


function show_hide_spec(i)
{
    var spec = $("spec_skill_" + i);
    spec.style.visibility == "hidden" ? spec.style.visibility = "visible" : spec.style.visibility = "hidden";
}
function updateInformation(el, id, type) {
	
	if (Element.extend(el).select('span.tooltipSpan')[0].empty()) {
		url = 'ask_information.php';
		parameters = {lessons_ID:id, method:'get'};
		
		s = el.select('span.tooltipSpan')[0];
		s.setStyle({height:'50px'}).insert(new Element('span').addClassName('progress').setStyle({margin:'auto',background:'url("themes/default/images/others/progress1.gif")'}));
		ajaxRequest(s, url, parameters, onUpdateInformation);
	}	
}
function onUpdateInformation(el, response) {
	//alert(el);alert(response);
	el.setStyle({height:'auto'}).update(response);
}
function archiveLesson(el, lesson) {
	parameters = {archive_lesson:lesson, method: 'get'};	
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onArchiveLesson);	
}
function onArchiveLesson(el, response) {
	new Effect.Fade(el.up().up());
}
