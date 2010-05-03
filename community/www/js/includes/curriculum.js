function ajaxPost(id, el, table_id) {
 if (table_id == 'coursesTable') {
  coursesAjaxPost(id, el, table_id);
 } else if (table_id == 'usersTable') {
  usersAjaxPost(id, el, table_id);
 }
}
function usersAjaxPost(login, el, table_id) {
 var url = location.toString();
 var parameters = {postAjaxRequest:'users', method: 'get'};

    if (login) {
     var checked = $('checked_'+login).checked;
     if (checked) {
      Object.extend(parameters, {insert: 1, users_LOGIN: login});
     } else {
      Object.extend(parameters, {insert: 0, users_LOGIN: login});
     }
    } else if (table_id) { // all mass assignments for all tables have the same management
        el.checked ? Object.extend(parameters, {table: table_id, addAll: 1}) : Object.extend(parameters, {table: table_id, removeAll: 1});
        if ($(table_id+'_currentFilter')) {
         Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
        }
    }

 ajaxRequest(el, url, parameters);
}

function coursesAjaxPost(id, el, table_id) {
 var url = location.toString();
 var parameters = {postAjaxRequest:'courses', method: 'get'};

    if (id) {
     var checked = $('course_'+id).checked;
     if (checked) {
      Object.extend(parameters, {insert: 1, courses_ID: id});
     } else {
      Object.extend(parameters, {insert: 0, courses_ID: id});
     }
    } else if (table_id) { // all mass assignments for all tables have the same management
        el.checked ? Object.extend(parameters, {table: table_id, addAll: 1}) : Object.extend(parameters, {table: table_id, removeAll: 1});
        if ($(table_id+'_currentFilter')) {
         Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
        }
    }

 ajaxRequest(el, url, parameters);
}

function assignCurriculumToGroup(el, id) {
 var url = location.toString();
 var parameters = {assign:'group', id:id, method: 'get'};
 ajaxRequest(el, url, parameters);
}
function assignCurriculumToUser(el, login) {
 var url = location.toString();
 var parameters = {assign:'user', login:login, method: 'get'};
 ajaxRequest(el, url, parameters);
}
