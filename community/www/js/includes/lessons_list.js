function approveCourseAssignment(el, course, user) {
 parameters = {course_id:course, users_login:user, ajax:'approval', method: 'get'};
 var url = window.location.toString();
 ajaxRequest(el, url, parameters, onCourseAssignment);
}
function cancelCourseAssignment(el, course, user) {
 parameters = {course_id:course, users_login:user, ajax:'cancel', method: 'get'};
 var url = window.location.toString();
 ajaxRequest(el, url, parameters, onCourseAssignment);
}
function onCourseAssignment(el, response) {
 try {
  if (response.evalJSON(true) && response.evalJSON(true).status) {
   new Effect.Fade(el.up().up());
  } else {
   alert(translations['_SOMEPROBLEMOCCURED']);
  }
 } catch (e) {
  alert('asd');
 }
}


function addGroupKey(el) {
 parameters = {group_key:$('group_key').value, method: 'get'};
 var url = window.location.toString();
 ajaxRequest(el, url, parameters, onAddGroupKey);
}
function onAddGroupKey(el, response) {
 if (!(w = findFrame(top, 'mainframe'))) {
  w = window;
 }

 w.location = w.location.toString() + (window.location.pathname.toString() ? '&' : '?') + 'message='+(translations['_YOUHAVEBEENSUCCESSFULLYADDEDTOTHEGROUP'])+'&message_type=success';
}
