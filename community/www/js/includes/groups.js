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

	//Element.extend(el);
    //var baseUrl =  'administrator.php?ctg=user_groups&edit_user_group='+editGroup+'&postAjaxRequest=1';
    if (login) {
        if (table_id == 'usersTable') {
            Object.extend(parameters, {login: login});
            //var checked  = $('checked_'+login).checked;
            //var url      = baseUrl + '&login='+login;
        } else if (table_id == 'lessonsTable') {
        	var checked  = $('lesson_'+login).checked;
        	if (checked) {
        		// add lesson - covering select change and onclick events
        		//var url      = baseUrl + '&insert=1&lessons_ID='+login; //+'&user_type=' + $('lesson_type_' + login).value;
                Object.extend(parameters, {insert: 1, lessons_ID: login});
        	} else {
        		// remove lesson
        		//var url      = baseUrl + '&insert=0&lessons_ID='+login;
                Object.extend(parameters, {insert: 0, lessons_ID: login});
        	}
        } else if (table_id == 'coursesTable') {
        	var checked  = $('course_'+login).checked;        
        	if (checked) {
        		// add lesson - covering select change and onclick events
        		//var url      = baseUrl + '&insert=1&courses_ID='+login; //+'&user_type=' + $('course_type_' + login).value;
                Object.extend(parameters, {insert: 1, courses_ID: login});
        	} else {
        		// remove lesson
        		var url      = baseUrl + '&insert=0&courses_ID='+login;
                Object.extend(parameters, {insert: 0, courses_ID: login});
        	}
        }
    } else if (table_id) {  // all mass assignments for all tables have the same management
        //el.checked ? url = baseUrl + '&table='+ table_id + '&addAll=1' : url = baseUrl + '&table='+ table_id + '&removeAll=1';
        el.checked ? Object.extend(parameters, {table: table_id, addAll: 1}) : Object.extend(parameters, {table: table_id, removeAll: 1});
        if ($(table_id+'_currentFilter')) {
            //url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
        	Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
        }
        //var img_id   = 'img_selectAll';
    }
    
	ajaxRequest(el, url, parameters);	    
}


function assignToGroupUsers(el, category) {
    //var url =  'administrator.php?ctg=user_groups&edit_user_group='+editGroup+'&postAjaxRequest=1&assign_to_all_users=' + category;
    var url = location.toString();
	parameters = {postAjaxRequest:1, assign_to_all_users: category, method: 'get'};
	ajaxRequest(el, url, parameters);	    
}
