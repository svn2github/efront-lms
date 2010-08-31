function toggleAutoComplete(selection) {
 if (selection) {
  $('private').checked = false;
  $('autocomplete').disabled = false;
  $('autocomplete').removeClassName('inactiveElement');
  switch (selection) {
   case 'course': autocompleter.url = "ask.php?ask_type=courses"; break;
   case 'lesson': autocompleter.url = "ask.php?ask_type=lessons"; break;
   case 'group' : autocompleter.url = "ask.php?ask_type=groups"; break;
   case 'branch': autocompleter.url = "ask.php?ask_type=branches"; break;
  }

 } else {
  $('select_type').options.selectedIndex = 0;
  $('autocomplete').disabled = true;
  $('autocomplete').addClassName('inactiveElement');
 }
 $('autocomplete').value = '';
 $('foreign_ID').value = '';
}
if ($('autocomplete_calendar')) {
 autocompleter =
 new Ajax.Autocompleter("autocomplete",
                           "autocomplete_calendar",
                           "ask.php?ask_type=courses", {paramName: "preffix",
                                               afterUpdateElement : function (t, li) {$('foreign_ID').value = li.id;},
                                               indicator : "busy"});
}
