function updateCourseBranchesInformation(el, login, type) {
	
	if (Element.extend(el).select('span.tooltipSpan')[0].empty()) {
		url = 'ask_information.php';
		parameters = {users_LOGIN:login, type:type, method:'get'};
		
		s = el.select('span.tooltipSpan')[0];
		s.setStyle({height:'50px'}).insert(new Element('span').addClassName('progress').setStyle({margin:'auto',background:'url("themes/default/images/others/progress1.gif")'}));
		ajaxRequest(s, url, parameters, onUpdateCourseBranchesInformation);
	}	
}
function onUpdateCourseBranchesInformation(el, response) {
	el.setStyle({height:'auto'}).update(response);
} 
function activateCourse(el, course) {
	if (el.className.match('red')) {
    	parameters = {activate_course:course, method: 'get'};
	} else {
		parameters = {deactivate_course:course, method: 'get'};
	}
    var url    = location.toString();
    ajaxRequest(el, url, parameters, onActivateCourse);
}
function onActivateCourse(el, response) {
    if (response == 0) {
    	setImageSrc(el, 16, "trafficlight_red.png");
        el.writeAttribute({alt:translationsToJS['_ACTIVATE'], title:translationsToJS['_ACTIVATE']});
    } else if (response == 1) {
    	setImageSrc(el, 16, "trafficlight_green.png");
        el.writeAttribute({alt:translationsToJS['_DEACTIVATE'], title:translationsToJS['_DEACTIVATE']});
    }
}

function deleteCourse(el, course) {
	parameters = {delete_course:course, method: 'get'};
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onDeleteCourse);	
}
function onDeleteCourse(el, response) {
	new Effect.Fade(el.up().up());
}


function ajaxPost(id, el, table_id) {
    //Since in the same page there are 3 ajax post lists, we create a "wrapper" which decides which one to call
    if (table_id == 'lessonsTable') {
        lessonsAjaxPost(id, el, table_id);
    } else if (table_id == 'skillsTable') {
        ajaxCourseSkillUserPost(1, id, el, table_id);
    } else if (table_id == 'branchesTable') {
    	ajaxCourseBranchPost(id, el, table_id);        
    } else if (table_id == 'usersTable') {
        usersAjaxPost(id, el, table_id);
    }
}

function lessonsAjaxPost(id, el, table_id) {
	var url = location.toString();
	var parameters = {postAjaxRequest:'lessons', method: 'get'};
    
    if (id) {
        Object.extend(parameters, {id: id});
    } else if (table_id && table_id == 'lessonsTable') {
        el.checked ? Object.extend(parameters, {addAll: 1}) : Object.extend(parameters, {removeAll: 1});
        if ($(table_id+'_currentFilter')) {
        	Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
        }
    }
	ajaxRequest(el, url, parameters, onLessonsAjaxPost);	
}
function onLessonsAjaxPost(el, response) {
	$('lessonsTable').select('select').each(function (s) {
		if (s.id.match('lesson_mode')) {
			s.hide();
		}
			
	}); 

	response.evalJSON(true).lessons.each(function (s) {
		if ($('lesson_mode_'+s)) {
			$('lesson_mode_'+s).show();
		}
	});
}
function setLessonMode(el, id, mode) {
	var url = location.toString();
	var parameters = {lesson:id, mode: mode, method: 'get'};
	ajaxRequest(el, url, parameters, onSetLessonMode);
}
function onSetLessonMode(el, response) {
	tables = sortedTables.size();
	for (var i = 0; i < tables; i++) {
		if (sortedTables[i].id.match('lessonsTable') && ajaxUrl[i]) {
			eF_js_rebuildTable(i, 0, 'has_lesson', 'desc');
		}
	}		
}

function usersAjaxPost(login, el, table_id) {
	var url = location.toString();
	var parameters = {postAjaxRequest:'users', method: 'get'};

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

//Used to associate courses and branches
function ajaxCourseBranchPost(id, el, table_id) {
	var url = location.toString();
	var parameters = {postAjaxRequest:'branches', method: 'get'};

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


// type: 1 - inserting/deleting the skill to an employee | 2 - changing the specification
// id: the users_login of the employee to get the skill
// el: the element of the form corresponding to that skill/course
// table_id: the id of the ajax-enabled table
function ajaxCourseSkillUserPost(type, id, el, table_id) {
	var url = location.toString();
	var parameters = {postAjaxRequest:'skills', method: 'get'};

    if (type == 1) {
        if (id) {
            Object.extend(parameters, {add_skill: id, insert: el.checked, specification: encodeURI(document.getElementById('spec_skill_'+id).value)});
        } else if (table_id && table_id == 'skillsTable') {
            el.checked ? Object.extend(parameters, {add_skill:1, addAll: 1}) : Object.extend(parameters, {add_skill:1, removeAll: 1});
            if ($(table_id+'_currentFilter')) {
            	Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
            }
        }
    } else if (type == 2) {
        if (id) {
            Object.extend(parameters, {add_skill: id, insert: true, specification: el.value});
        }
    } else {
        return false;
    }
	ajaxRequest(el, url, parameters);	

}

function show_hide_spec(i)
{
    var spec = $("spec_skill_" + i);
    spec.style.visibility == "hidden" ? spec.style.visibility = "visible" : spec.style.visibility = "hidden";
}
function archiveCourse(el, course) {
	parameters = {archive_course:course, method: 'get'};	
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onArchiveCourse);	
}
function onArchiveCourse(el, response) {
	new Effect.Fade(el.up().up());
}

function addInstance(el) {
	el.insert(new Element('img', {src:'themes/default/images/others/progress1.gif'}).addClassName('handle'));
	
	parameters = {add_instance:1, method: 'get'};	
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onAddInstance);		
}
function onAddInstance(el, response) {
	el.select('img.handle')[0].remove();
	tables = sortedTables.size();
	for (var i = 0; i < tables; i++) {
		if (sortedTables[i].id.match('instancesTable') && ajaxUrl[i]) {
			eF_js_rebuildTable(i, 0, 'null', 'desc');
		}
	}	
}

function toggleUserAccess(el, id, type) {
	parameters = {ajax:'toggle_user', type: type, user: id, method: 'get'};
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
function resetProgress(el, login) {
	var url = location.toString();
	var parameters = {reset_user:login, method: 'get'};
	ajaxRequest(el, url, parameters, onResetProgress);
}
function onResetProgress(el, response) {
	setImageSrc(el, 16, 'success');
	new Effect.Fade(el, {afterFinish:function (s) {setImageSrc(el, 16, 'refresh');el.show();}});			
}