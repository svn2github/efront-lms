function activateGroup(el, group) {
	if (el.className.match('red')) {
    	parameters = {activate_user_group:group, method: 'get'};
	} else {
		parameters = {deactivate_user_group:group, method: 'get'};
	}
	var url = location.toString();
    ajaxRequest(el, url, parameters, onActivateGroup);
}
function onActivateGroup(el, response) {
    if (response == 0) {
    	setImageSrc(el, 16, "trafficlight_red.png");
        el.writeAttribute({alt:activate, title:activate});
    } else if (response == 1) {
    	setImageSrc(el, 16, "trafficlight_green.png");
        el.writeAttribute({alt:deactivate, title:deactivate});
    }
}

function deleteGroup(el, group) {
	parameters = {delete_user_group:group, method: 'get'};
	var url = location.toString();
	ajaxRequest(el, url, parameters, onDeleteGroup);	
}
function onDeleteGroup(el, response) {
	new Effect.Fade(el.up().up());
}

function ajaxPost(login, el, table_id) {
	var url = location.toString();
	var parameters = {postAjaxRequest:1, method: 'get'};

    if (login) {
        if (table_id == 'usersTable') {
            Object.extend(parameters, {login: login});
        } else if (table_id == 'lessonsTable') {
        	var checked  = $('lesson_'+login).checked;
        	if (checked) {
                Object.extend(parameters, {insert: 1, lessons_ID: login});
        	} else {
                Object.extend(parameters, {insert: 0, lessons_ID: login});
        	}
        } else if (table_id == 'coursesTable') {
        	var checked  = $('course_'+login).checked;        
        	if (checked) {
                Object.extend(parameters, {insert: 1, courses_ID: login});
        	} else {
                Object.extend(parameters, {insert: 0, courses_ID: login});
        	}
        }
    } else if (table_id) {  // all mass assignments for all tables have the same management
        el.checked ? Object.extend(parameters, {table: table_id, addAll: 1}) : Object.extend(parameters, {table: table_id, removeAll: 1});
        if ($(table_id+'_currentFilter')) {
        	Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
        }
    }
    
	ajaxRequest(el, url, parameters);	    
}


function assignToGroupUsers(el, category) {
	Element.extend(el).insert(new Element('img', {src:'themes/default/images/others/progress1.gif'}).addClassName('handle'));

	var url = location.toString();
	parameters = {postAjaxRequest:1, assign_to_all_users: category, method: 'get'};
	ajaxRequest(el, url, parameters, onAssignToGroupUsers);	    
}
function onAssignToGroupUsers(el, response) {
	 el.down().src='themes/default/images/others/transparent.gif';  
	 el.down().className='sprite16 sprite16-success';  
	 Effect.Fade(el.down(), {afterFinish:function() {el.down().remove()}});
	 
}
