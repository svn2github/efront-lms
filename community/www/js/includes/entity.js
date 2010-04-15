function deleteEntity(el, id, params) {
 parameters = {'delete':id, method: 'get'};
 if (params) {
  Object.extend(parameters, params);
 }

 var url = location.toString();
 ajaxRequest(el, url, parameters, onDeleteEntity);
}
function onDeleteEntity(el, response) {
 new Effect.Fade(el.up().up());
}
function activateEntity(el, id, params) {
 parameters = {'activate':id, method: 'get'};
 if (params) {
  Object.extend(parameters, params);
 }

 var url = location.toString();
 ajaxRequest(el, url, parameters, onActivateEntity);
}
function onActivateEntity(el, response) {
 el.hide();
 el.previous().show();
}
function deactivateEntity(el, id, params) {
 parameters = {'deactivate':id, method: 'get'};
 if (params) {
  Object.extend(parameters, params);
 }

 var url = location.toString();
 ajaxRequest(el, url, parameters, onDeactivateEntity);
}
function onDeactivateEntity(el, response) {
 el.hide();
 el.next().show();
}
