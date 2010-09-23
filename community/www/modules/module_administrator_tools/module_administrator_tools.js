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
