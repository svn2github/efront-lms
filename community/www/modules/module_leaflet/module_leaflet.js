if ($('autocomplete_leaflet_branches')) {
	new Ajax.Autocompleter("autocomplete", 
						   "autocomplete_leaflet_branches", 
						   "ask.php?ask_type=branches", {paramName: "preffix", 
													afterUpdateElement : function (t, li) {$('leaflet_branch_value').value = li.id;}, 
													indicator : "busy"}); 
}
