function logoutUser(el, user) {
 parameters = {logout:user, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, location.toString(), parameters, onLogoutUser);
}
function onLogoutUser(el, response) {
 if (response.evalJSON(true).status) {
  new Effect.Fade(Element.extend(el).up().up());
 }
}
