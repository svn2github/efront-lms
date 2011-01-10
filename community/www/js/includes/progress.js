function resetProgressInLesson(el, login) {
 var url = location.toString();
 var parameters = {reset_user:login, method: 'get'};
 ajaxRequest(el, url, parameters, onResetProgressInLesson);
}
function onResetProgressInLesson(el, response) {
 eF_js_redrawPage('usersTable', true);
}
