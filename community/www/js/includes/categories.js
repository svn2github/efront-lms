function activateCategory(el, category) {
 if (el.className.match('red')) {
     parameters = {activate_direction:category, method: 'get'};
 } else {
  parameters = {deactivate_direction:category, method: 'get'};
 }
    var url = location.toString();
    ajaxRequest(el, url, parameters, onActivateCategory);
}
function onActivateCategory(el, response) {
    if (response == 0) {
     setImageSrc(el, 16, "trafficlight_red.png");
        el.writeAttribute({alt:activate, title:activate});
    } else if (response == 1) {
     setImageSrc(el, 16, "trafficlight_green.png");
        el.writeAttribute({alt:deactivate, title:deactivate});
    }
}

function deleteCategory(el, category) {
 parameters = {delete_direction:category, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onDeleteCategory);
}
function onDeleteCategory(el, response) {
 new Effect.Fade(el.up().up());
}

function ajaxPost(id, el, table_id) {
    var url = location.toString();

 parameters = {id:id.replace('course_', '').replace('lesson_', ''),
      directions_ID: $(id).options[$(id).options.selectedIndex].value,
      method: 'get'};
 if (table_id == 'lessonsTable') {
  Object.extend(parameters, {lessonsPostAjaxRequest:1});
 } else {
  Object.extend(parameters, {coursesPostAjaxRequest:1});
 }

 ajaxRequest(el, url, parameters, onAjaxPost);
}
function onAjaxPost(el, response) {
 tables = sortedTables.size();
 for (var i = 0; i < tables; i++) {
  if (sortedTables[i].id.match('lessonsTable') && ajaxUrl[i]) {
   eF_js_rebuildTable(i, 0, 'null', 'desc');
  } else if (sortedTables[i].id.match('coursesTable') && ajaxUrl[i]) {
   eF_js_rebuildTable(i, 0, 'null', 'desc');
  }
 }
}
