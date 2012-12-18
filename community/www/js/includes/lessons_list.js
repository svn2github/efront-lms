function approveCourseAssignment(el, course, user) {
	parameters = {course_id:course, users_login:user, ajax:'approval', method: 'get'};
	var url    = window.location.toString();
	ajaxRequest(el, url, parameters, onCourseAssignment);		
}
function cancelCourseAssignment(el, course, user) {
	parameters = {course_id:course, users_login:user, ajax:'cancel', method: 'get'};
	var url    = window.location.toString();
	ajaxRequest(el, url, parameters, onCourseAssignment);			
}
function onCourseAssignment(el, response) {
	if (response.evalJSON(true) && response.evalJSON(true).status) {
		new Effect.Fade(el.up().up());
	} else {
		alert(translations['_SOMEPROBLEMOCCURED']);
	}
}

function addGroupKey(el) {
	parameters = {group_key:$('group_key').value, method: 'get'};
	var url    = window.location.toString();
	ajaxRequest(el, url, parameters, onAddGroupKey);	
}
function onAddGroupKey(el, response) {
	
	if (!(w = findFrame(top, 'mainframe'))) {
		w = window;
	}
	if (response == 'false') {
		w.location = w.location.toString();
	} else {
		w.location = w.location.toString().replace(/\?.*/, '?ctg=lessons');
	}
}