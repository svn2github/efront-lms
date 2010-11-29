if ($('module_administrator_tools_autocomplete_users_div')) {
 autocompleter =
  new Ajax.Autocompleter("module_administrator_tools_autocomplete_users",
    "module_administrator_tools_autocomplete_users_div",
    "ask.php?ask_type=users", {paramName: "preffix",
   afterUpdateElement : function (t, li) {$('module_administrator_tools_users_LOGIN').value = li.id;},
   indicator : "module_administrator_tools_busy"});
}
function activate(el, action) {
 Element.extend(el);
 parameters = {ajax:1, method: 'get'};
 el.down().className.match(/inactiveImage/) ? parameters = Object.extend(parameters, {activate:action}) : parameters = Object.extend(parameters, {deactivate:action}) ;
 el.down().setAttribute('src', 'themes/default/images/others/progress_big.gif');
 el.down().removeClassName('sprite32');
 var url = location.toString();
 ajaxRequest(el, url, parameters, onActivate);

}
function onActivate(el, response) {
 el.down().setAttribute('src', 'themes/default/images/others/transparent.gif');
 el.down().addClassName('sprite32');
 if (el.down().className.match(/inactiveImage/)) {
  el.down().removeClassName('inactiveImage');
 } else {
  el.down().addClassName('inactiveImage');
 }

 if (top.sideframe) {
  top.sideframe.location.reload();
 }
}

function ajaxPost(login, el, table_id) {
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
 ajaxRequest(el, url, parameters);
}

if ($('module_administrator_tools_autocomplete_lessons_div')) {
 new Ajax.Autocompleter("autocomplete",
         "module_administrator_tools_autocomplete_lessons_div",
         "ask.php?ask_type=lessons", {paramName: "preffix",
              afterUpdateElement : function (t, li) {document.location = document.location.toString().replace(/&tab=\w*/g, '').replace(/&lessons_ID=\d*/g, '')+"&tab=set_course_lesson_users"+"&lessons_ID="+li.id;},
              indicator : "busy"});
}
