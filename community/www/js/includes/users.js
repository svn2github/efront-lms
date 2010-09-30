function activateUser(el, user) {
 if (el.className.match('red')) {
     parameters = {activate_user:user, method: 'get'};
 } else {
  parameters = {deactivate_user:user, method: 'get'};
 }
    var url = location.toString();
    ajaxRequest(el, url, parameters, onActivateUser);
}
function onActivateUser(el, response) {
    if (response == 0) {
     setImageSrc(el, 16, "trafficlight_red.png");
        el.writeAttribute({alt:activate, title:activate});
        el.up().up().addClassName('deactivatedTableElement');
    } else if (response == 1) {
     setImageSrc(el, 16, "trafficlight_green.png");
        el.writeAttribute({alt:deactivate, title:deactivate});
        el.up().up().removeClassName('deactivatedTableElement');
    }
}

function deleteUser(el, user) {
 parameters = {delete_user:user, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onDeleteUser);
}
function onDeleteUser(el, response) {
 new Effect.Fade(el.up().up());
}
function archiveUser(el, user) {
 parameters = {archive_user:user, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onArchiveUser);
}
function onArchiveUser(el, response) {
 new Effect.Fade(el.up().up());
}
function updateInformation(el, login, type) {

 if (Element.extend(el).select('span.tooltipSpan')[0].empty()) {
  url = 'ask_information.php';
  parameters = {users_LOGIN:login, type:type, method:'get'};

  s = el.select('span.tooltipSpan')[0];
  s.setStyle({height:'50px'}).insert(new Element('span').addClassName('progress').setStyle({margin:'auto',background:'url("themes/default/images/others/progress1.gif")'}));
  ajaxRequest(s, url, parameters, onUpdateInformation);
 }
}
function onUpdateInformation(el, response) {
 //alert(el);alert(response);
 el.setStyle({height:'auto'}).update(response);
}
