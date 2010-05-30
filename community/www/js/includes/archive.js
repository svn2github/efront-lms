function deleteArchive(el, id) {
 parameters = {'delete':new Array(id).toJSON(), method: 'get'};
 ajaxRequest(el, url, parameters, onDeleteArchive);
}
function onDeleteArchive(el, response) {
 new Effect.Fade(el.up().up());
}
function restoreArchive(el, id) {
 parameters = {'restore':new Array(id).toJSON(), method: 'get'};
 ajaxRequest(el, url, parameters, onRestoreArchive);
}
function onRestoreArchive(el, response) {
 new Effect.Fade(el.up().up());
}
function deleteSelected(el, tableId) {
 entities = new Array();
 $(tableId).select("input[type=checkbox]").each(function (s) {
  if (s.checked && s.id) {
   entities.push(s.value);
  }
 });
 parameters = {'delete':entities.toJSON(), method: 'get'};
 ajaxRequest(el, url, parameters, onDeleteRestoreSelected);
 window.archiveTableId = tableId;
}
function restoreSelected(el, tableId) {
 entities = new Array();
 $(tableId).select("input[type=checkbox]").each(function (s) {
  if (s.checked && s.id) {
   entities.push(s.value);
  }
 });
 parameters = {'restore':entities.toJSON(), method: 'get'};
 ajaxRequest(el, url, parameters, onDeleteRestoreSelected);
 window.archiveTableId = tableId;
}
function onDeleteRestoreSelected(el, response) {
 $(window.archiveTableId).select("input[type=checkbox]").each(function (s) {
  if (s.checked && s.id) {
   new Effect.Fade(s.up().up());
  }
 });
}
