function activateField(el, field) {
 if (el.className.match('red')) {
     parameters = {activate_field:field, method: 'get'};
 } else {
  parameters = {deactivate_field:field, method: 'get'};
 }
    var url = location.toString();
    ajaxRequest(el, url, parameters, onActivateField);
}
function onActivateField(el, response) {
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

function deleteField(el, field) {
 parameters = {delete_field:field, method: 'get'};
 var url = 'administrator.php?ctg=user_profile';
 ajaxRequest(el, url, parameters, onDeleteField);
}
function onDeleteField(el, response) {
 new Effect.Fade(el.up().up());
}
