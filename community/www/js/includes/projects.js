function deleteProject(el, project) {
 parameters = {delete_project:project, method: 'get'};
 var url = 'professor.php?ctg=projects';
 ajaxRequest(el, url, parameters, onDeleteProject);
}
function onDeleteProject(el, response) {
 new Effect.Fade(el.up().up());
}

function ajaxPost(id, el, table_id) {
    //Since in the same page there are 2 ajax post lists, we create a "wrapper" which decides which one to call
    if (table_id == 'usersTable') {
        usersAjaxPost(id, el, table_id);
    } else if (table_id == 'resultsTable') {
     resultsAjaxPost(1, id, el, table_id);
    }
}

function usersAjaxPost(login, el, table_id) {
    Element.extend(el);
    var baseUrl = 'professor.php?ctg=projects&edit_project='+editProject+'&postAjaxRequest=1';
    if (login) {
        var checked = $('checked_'+login).checked;
        var url = baseUrl + '&login='+login;
    } else if (table_id && table_id == 'usersTable') {
        el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
        if ($(table_id+'_currentFilter')) {
            url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
        }
    }

 parameters = {method: 'get'};
 ajaxRequest(el, url, parameters);

}

function resultsAjaxPost(login, el, table_id) {

    var baseUrl = 'professor.php?ctg=projects&project_results='+editProject+'&postAjaxRequest=1';
    //var comments = $('comments_'+login).value;
    var grade = $('grade_'+login).value;
   // var url      = baseUrl + '&login='+login+'&grade='+grade+'&comments='+comments;
 var url = baseUrl + '&login='+login+'&grade='+grade;

 parameters = {method: 'get'};
 ajaxRequest(el, url, parameters);

}

function resetUser(login, el) {
 url = location.toString();
 parameters = {reset_user:login, postAjaxRequest: 1, method: 'get'};
 ajaxRequest(el, url, parameters, onResetUser);
}

function onResetUser() {
 tables = sortedTables.size();
 for (var i = 0; i < tables; i++) {
  if (sortedTables[i].id.match('resultsTable') && ajaxUrl[i]) {
   eF_js_rebuildTable(i, 0, 'null', 'desc');
  }
 }
}
function onSortedTableComplete() {
 var heightValue;
 if (sortedTables[tableIndex].getDimensions().height != 0) {
  heightValue = parseInt(sortedTables[tableIndex].getDimensions().height+50);
 } else {
  heightValue = 0;
 }
 if (sortedTables[tableIndex].id == 'filesTable') {
  $('filemanager_cell').setStyle({width:sortedTables[tableIndex].getDimensions().width+'px', height:heightValue+'px', verticalAlign:'top'});
 }
}

function insertatcursor(myField, myValue) {
        if (document.selection) {
            myField.focus();
            sel = document.selection.createRange();
            sel.text = myValue;
        }
        else if (myField.selectionStart || myField.selectionStart == '0') {
            var startPos = myField.selectionStart;
            var endPos = myField.selectionEnd;
            myField.value = myField.value.substring(0, startPos)+ myValue+ myField.value.substring(endPos, myField.value.length);
        } else {
            myField.value += myValue;
        }
    }
