if ($('module_administrator_tools_autocomplete_users_div')) {
 autocompleter =
  new Ajax.Autocompleter("module_administrator_tools_autocomplete_users",
    "module_administrator_tools_autocomplete_users_div",
    "ask.php?ask_type=users", {paramName: "preffix",
   afterUpdateElement : function (t, li) {$('module_administrator_tools_users_LOGIN').value = li.id;},
   indicator : "module_administrator_tools_busy"});
}
