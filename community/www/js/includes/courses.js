function activateCourse(el, course) {
	if (el.className.match('red')) {
    	parameters = {activate_course:course, method: 'get'};
	} else {
		parameters = {deactivate_course:course, method: 'get'};
	}
    var url    = 'administrator.php?ctg=courses';
    ajaxRequest(el, url, parameters, onActivateCourse);
}
function onActivateCourse(el, response) {
    if (response == 0) {
    	setImageSrc(el, 16, "trafficlight_red.png");
        el.writeAttribute({alt:activate, title:activate});
    } else if (response == 1) {
    	setImageSrc(el, 16, "trafficlight_green.png");
        el.writeAttribute({alt:deactivate, title:deactivate});
    }
}

function deleteCourse(el, course) {
	parameters = {delete_course:course, method: 'get'};
	var url    = 'administrator.php?ctg=courses';
	ajaxRequest(el, url, parameters, onDeleteCourse);	
}
function onDeleteCourse(el, response) {
	new Effect.Fade(el.up().up());
}


function ajaxPost(id, el, table_id) {
    //Since in the same page there are 3 ajax post lists, we create a "wrapper" which decides which one to call
    if (table_id == 'lessonsTable') {
        lessonsAjaxPost(id, el, table_id);
    } else if(table_id == 'skillsTable') {
        ajaxCourseSkillUserPost(1, id, el, table_id)
    } else {
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
	ajaxRequest(el, url, parameters);	
    
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

// type: 1 - inserting/deleting the skill to an employee | 2 - changing the specification
// id: the users_login of the employee to get the skill
// el: the element of the form corresponding to that skill/course
// table_id: the id of the ajax-enabled table
function ajaxCourseSkillUserPost(type, id, el, table_id) {
	var url = location.toString();
	var parameters = {postAjaxRequest:1, method: 'get'};

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
